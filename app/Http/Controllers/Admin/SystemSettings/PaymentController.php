<?php

namespace App\Http\Controllers\Admin\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\DepositSetting;
use App\Models\WithdrawalSetting;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // === Deposit Settings ===
    public function getDepositSettings()
    {
        return response()->json(DepositSetting::first());
    }

    public function updateDepositSettings(Request $request)
    {
        $data = $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0',
            'allow_manual_approval' => 'boolean',
        ]);

        $settings = DepositSetting::firstOrNew([]);
        $settings->fill($data)->save();

        return response()->json(['message' => 'Deposit settings updated successfully.']);
    }

    // === Withdrawal Settings ===
    public function getWithdrawalSettings()
    {
        return response()->json(WithdrawalSetting::first());
    }

    public function updateWithdrawalSettings(Request $request)
    {
        $data = $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|min:0',
            'daily_total_limit' => 'required|numeric|min:0',
            'require_2fa' => 'boolean',
        ]);

        $settings = WithdrawalSetting::firstOrNew([]);
        $settings->fill($data)->save();

        return response()->json(['message' => 'Withdrawal settings updated successfully.']);
    }

    // === Payment Gateways ===
    public function getPaymentGateways()
    {
        return response()->json(PaymentGateway::all());
    }

    public function updatePaymentGateway(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->update([
            'config' => $request->input('config', []),
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['message' => "{$gateway->name} configuration updated successfully."]);
    }

    public function addCustomGateway(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:payment_gateways,name',
            'config' => 'nullable|array',
        ]);

        PaymentGateway::create($data + ['is_active' => false]);

        return response()->json(['message' => 'Custom payment gateway added successfully.']);
    }
}

