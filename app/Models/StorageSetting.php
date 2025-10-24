<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageSetting extends Model
{
    use HasFactory;

    protected $fillable = ['driver', 'config', 'is_active'];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];
}
