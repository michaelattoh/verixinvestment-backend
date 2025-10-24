<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\StorageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageSettingsController extends Controller
{
    public function index()
    {
        return response()->json([
            'storages' => StorageSetting::all(),
        ]);
    }

    public function update(Request $request, $driver)
    {
        $data = $request->validate([
            'config' => 'required|array',
            'is_active' => 'boolean',
        ]);

        // deactivate others if setting this one active
        if (!empty($data['is_active']) && $data['is_active'] === true) {
            StorageSetting::where('driver', '!=', $driver)->update(['is_active' => false]);
        }

        $storage = StorageSetting::updateOrCreate(
            ['driver' => $driver],
            ['config' => $data['config'], 'is_active' => $data['is_active'] ?? false]
        );

        // log the update
        Log::info("Storage settings updated for driver: {$driver}");

        return response()->json(['message' => 'Storage settings updated successfully', 'data' => $storage]);
    }

    public function testConnection($driver)
    {
        try {
            $storage = StorageSetting::where('driver', $driver)->firstOrFail();
            $config = $storage->config;

            // Example: for S3 or similar drivers
            if (in_array($driver, ['s3', 'digitalocean', 'wasabi', 'backblaze', 'gcs'])) {
                $test = Storage::build([
                    'driver' => 's3',
                    'key' => $config['key'] ?? null,
                    'secret' => $config['secret'] ?? null,
                    'region' => $config['region'] ?? null,
                    'bucket' => $config['bucket'] ?? null,
                    'endpoint' => $config['endpoint'] ?? null,
                ])->exists('/');
            } else {
                $test = Storage::disk('local')->exists('/');
            }

            return response()->json(['success' => $test ? true : false]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
