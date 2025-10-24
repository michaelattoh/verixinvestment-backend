<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalSetting extends Model
{
    protected $fillable = ['min_amount', 'max_amount', 'daily_total_limit', 'require_2fa'];
}
