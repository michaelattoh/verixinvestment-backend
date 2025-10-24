<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Services\StorageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    // trigger a backup (returns output)
    public function runBackup(Request $request)
    {
        // WARNING: ensure only authorized admins can run
        $output = [];
        Artisan::call('backup:run', ['--only-db' => false], $output);
        $result = Artisan::output(); // combined output
        return response()->json(['message' => 'Backup started', 'output' => $result]);
    }

    // list backups (if using spatie it saves to storage)
    public function listBackups()
    {
        $disk = config('backup.backup.destination.disks')[0] ?? 'local';
        $path = config('backup.backup.name') . '/';
        // sample: list files on storage disk
        $files = \Storage::disk($disk)->allFiles($path);
        return response()->json(['files' => $files]);
    }

    public function index()
    {
        $backups = Backup::orderBy('created_at', 'desc')->get();
        return response()->json($backups);
    }

    public function createBackup()
    {
        Artisan::call('system:backup');
        return response()->json(['message' => 'Backup created successfully']);
    }

    public function download($id)
    {
        $backup = Backup::findOrFail($id);
        $disk = StorageManager::getActiveDisk();

        if (!$disk->exists($backup->path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $fileContent = $disk->get($backup->path);
        return response($fileContent, 200)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', 'attachment; filename="'.$backup->name.'"');
    }

    public function delete($id)
    {
        $backup = Backup::findOrFail($id);
        $disk = StorageManager::getActiveDisk();

        if ($disk->exists($backup->path)) {
            $disk->delete($backup->path);
        }

        $backup->delete();
        return response()->json(['message' => 'Backup deleted successfully']);
    }
}
