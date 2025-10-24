<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::updated(function ($model) {
            $user = Auth::user();

            AuditLog::create([
                'event' => 'updated',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'user_id' => $user?->id,
                'user_type' => $user?->role ?? 'system',
                'changes' => [
                    'old' => $model->getOriginal(),
                    'new' => $model->getChanges(),
                ],
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ]);
        });

        static::created(function ($model) {
            $user = Auth::user();

            AuditLog::create([
                'event' => 'created',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'user_id' => $user?->id,
                'user_type' => $user?->role ?? 'system',
                'changes' => ['new' => $model->attributesToArray()],
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ]);
        });

        static::deleted(function ($model) {
            $user = Auth::user();

            AuditLog::create([
                'event' => 'deleted',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'user_id' => $user?->id,
                'user_type' => $user?->role ?? 'system',
                'changes' => ['old' => $model->getOriginal()],
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'),
            ]);
        });
    }
}
