<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\SystemSetting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dynamically configure backup scheduling
$settings = SystemSetting::where('group', 'backup')->pluck('value', 'key')->toArray();

if (!empty($settings['auto_backup_enabled']) && $settings['auto_backup_enabled']) {
    $time = $settings['backup_time'] ?? '02:00';
    $frequency = $settings['frequency'] ?? 'daily';

    $task = Schedule::command('system:backup');

    match ($frequency) {
        'daily' => $task->dailyAt($time),
        'weekly' => $task->weeklyOn(1, $time),
        'monthly' => $task->monthlyOn(1, $time),
        default => $task->dailyAt($time),
    };
} else {
    // Fallback default (e.g., daily at 2 AM)
    Schedule::command('system:backup')->dailyAt('02:00');
}
