<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\InvestmentSetting;
use Illuminate\Http\Request;

class InvestmentSettingController extends Controller
{
    public function show()
    {
        $settings = InvestmentSetting::first();
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'daily_savings_fee' => 'numeric|min:0|max:100',
            'weekly_savings_fee' => 'numeric|min:0|max:100',
            'monthly_savings_fee' => 'numeric|min:0|max:100',
            'fixed_investment_fee' => 'numeric|min:0|max:100',
            'agricultural_fee' => 'numeric|min:0|max:100',
            'default_vendor_commission' => 'numeric|min:0|max:100',
            'max_vendor_commission' => 'numeric|min:0|max:100',
        ]);

        $settings = InvestmentSetting::firstOrCreate([]);
        $settings->update($validated);

        return response()->json([
            'message' => 'Investment settings updated successfully',
            'data' => $settings
        ]);
    }
}
