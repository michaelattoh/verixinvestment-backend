<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class BackupSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'group' => 'backup',
                'key' => 'auto_backup_enabled',
                'value' => false,
                'type' => 'boolean',
                'description' => 'Enable or disable automatic database backups',
            ],
            [
                'group' => 'backup',
                'key' => 'frequency',
                'value' => 'daily',
                'type' => 'string',
                'description' => 'Backup frequency (daily, weekly, monthly)',
            ],
            [
                'group' => 'backup',
                'key' => 'backup_time',
                'value' => '02:00',
                'type' => 'string',
                'description' => 'Time of day backups run',
            ],
            [
                'group' => 'backup',
                'key' => 'retention_days',
                'value' => 30,
                'type' => 'integer',
                'description' => 'Days to retain old backups',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }
}
