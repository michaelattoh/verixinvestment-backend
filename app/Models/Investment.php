<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    protected $fillable = [
        'user_id', 'transaction_id', 'type', 'amount', 'goal_amount', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
