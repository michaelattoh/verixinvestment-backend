<?php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

if (!function_exists('log_activity')) {
    /**
     * Log an API-level action (non-model related).
     *
     * @param string $action
     * @param string|null $description
     * @param array|null $meta
     * @return void
     */
    function log_activity(string $action, ?string $description = null, ?array $meta = [])
    {
        $user = Auth::user();

        AuditLog::create([
            'event' => $action, // e.g. "viewed", "exported", "logged_in"
            'model_type' => null,
            'model_id' => null,
            'user_id' => $user?->id,
            'user_type' => $user?->role ?? 'guest',
            'changes' => $meta ? json_encode($meta) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
        ]);
    }
}
