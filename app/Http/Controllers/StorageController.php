<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Exception;

class StorageController extends Controller
{
    // Test connection without saving
    public function testConnection(Request $request)
    {
        $driver = $request->input('driver');
        $config = $request->input('config', []);

        return response()->json($this->validateAndTest($driver, $config));
    }

    // Save storage settings (only if test succeeds)
    public function save(Request $request)
    {
        $driver = $request->input('driver');
        $config = $request->input('config', []);
        $isActive = $request->boolean('is_active', false);

        $result = $this->validateAndTest($driver, $config);
        if (!$result['success']) {
            return response()->json($result, 400);
        }

        if ($isActive) {
            StorageSetting::where('is_active', true)->update(['is_active' => false]);
        }

        $setting = StorageSetting::updateOrCreate(
            ['driver' => $driver],
            ['config' => $config, 'is_active' => $isActive]
        );

        return response()->json([
            'success' => true,
            'message' => 'Storage configuration saved successfully.',
            'data' => $setting
        ]);
    }

    // Reusable internal method
    private function validateAndTest($driver, $config)
    {
        try {
            switch ($driver) {
                case 's3':
                case 'digitalocean':
                case 'wasabi':
                case 'backblaze':
                case 'gcs':
                    Config::set('filesystems.disks.temp_test', [
                        'driver' => 's3',
                        'key' => $config['key'] ?? null,
                        'secret' => $config['secret'] ?? null,
                        'region' => $config['region'] ?? null,
                        'bucket' => $config['bucket'] ?? null,
                        'endpoint' => $config['endpoint'] ?? null,
                    ]);
                    break;

                case 'local':
                    Config::set('filesystems.disks.temp_test', [
                        'driver' => 'local',
                        'root' => storage_path('app/test-storage'),
                    ]);
                    break;

                default:
                    return [
                        'success' => false,
                        'message' => 'Unsupported driver type.'
                    ];
            }

            $disk = Storage::disk('temp_test');
            $disk->put('test_connection.txt', 'testing');
            $disk->delete('test_connection.txt');

            return ['success' => true, 'message' => 'Connection successful!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }
}
