<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'actor_id', 'actor_type', 'action', 'target_type', 'target_id', 'read_at'
    ];

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
}
