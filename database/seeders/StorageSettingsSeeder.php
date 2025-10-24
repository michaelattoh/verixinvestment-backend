<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StorageSetting;

class StorageSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $storages = [
            'local' => [],
            's3' => ['key' => '', 'secret' => '', 'region' => '', 'bucket' => '', 'endpoint' => ''],
            'digitalocean' => ['key' => '', 'secret' => '', 'region' => '', 'bucket' => '', 'endpoint' => ''],
            'wasabi' => ['key' => '', 'secret' => '', 'region' => '', 'bucket' => '', 'endpoint' => ''],
            'backblaze' => ['key' => '', 'secret' => '', 'bucket' => '', 'endpoint' => ''],
            'gcs' => ['key_file' => '', 'bucket' => '', 'project_id' => ''],
        ];

        foreach ($storages as $driver => $config) {
            StorageSetting::updateOrCreate(['driver' => $driver], [
                'config' => $config,
                'is_active' => $driver === 'local'
            ]);
        }
    }
}
