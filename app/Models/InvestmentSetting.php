<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;


class InvestmentSetting extends Model
{
    use Auditable;
    
    protected $fillable = [
        'daily_savings_fee',
        'weekly_savings_fee',
        'monthly_savings_fee',
        'fixed_investment_fee',
        'agricultural_fee',
        'default_vendor_commission',
        'max_vendor_commission',
    ];
}
