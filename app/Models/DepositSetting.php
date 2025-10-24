<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositSetting extends Model
{
    protected $fillable = ['min_amount', 'max_amount', 'allow_manual_approval'];
}
