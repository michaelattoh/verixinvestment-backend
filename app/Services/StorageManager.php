<?php

namespace App\Services;

use App\Models\StorageSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class StorageManager
{
    public static function applyActiveStorage(): void
    {
        $active = StorageSetting::where('is_active', true)->first();

        if (!$active) {
            Config::set('filesystems.default', 'local');
            return;
        }

        $driver = $active->driver;
        $config = $active->config ?? [];

        switch ($driver) {
            case 's3':
            case 'digitalocean':
            case 'wasabi':
            case 'backblaze':
            case 'gcs':
                Config::set('filesystems.disks.dynamic', [
                    'driver' => 's3',
                    'key' => $config['key'] ?? null,
                    'secret' => $config['secret'] ?? null,
                    'region' => $config['region'] ?? null,
                    'bucket' => $config['bucket'] ?? null,
                    'endpoint' => $config['endpoint'] ?? null,
                    'visibility' => 'public',
                ]);
                Config::set('filesystems.default', 'dynamic');
                break;

            case 'local':
            default:
                Config::set('filesystems.default', 'local');
                break;
        }
    }

    public static function getActiveDisk()
    {
        self::applyActiveStorage();
        return Storage::disk(config('filesystems.default'));
    }
}
