<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Backup;
use App\Services\StorageManager;
use Carbon\Carbon;

class SystemBackup extends Command
{
    protected $signature = 'system:backup';
    protected $description = 'Create a database backup and store it in the active storage';

    public function handle()
    {
        $timestamp = Carbon::now()->format('Y_m_d_His');
        $fileName = "backup_{$timestamp}.sql";

        $disk = StorageManager::getActiveDisk();
        $localPath = storage_path("app/{$fileName}");

        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST');

        // ✅ Add --no-tablespaces to avoid permission errors
        $command = "mysqldump --no-tablespaces -h {$dbHost} -u {$dbUser} --password=\"{$dbPass}\" {$dbName} > {$localPath}";

        // ✅ Actually execute the dump command
        system($command);

        if (!file_exists($localPath)) {
            $this->error('Backup failed — no dump file created.');
            return 1;
        }

        $disk->put($fileName, file_get_contents($localPath));
        $size = round(filesize($localPath) / 1024 / 1024, 2) . ' MB';

        Backup::create([
            'name' => $fileName,
            'path' => $fileName,
            'size' => $size,
        ]);

        unlink($localPath);

        $this->info("Backup created successfully ({$size}) and stored in active storage.");
        return 0;
    }
}
