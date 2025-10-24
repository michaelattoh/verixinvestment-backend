<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Validator;

class PaymentGatewayController extends Controller
{
    /**
     * Fetch all gateways
     */
    public function index()
    {
        $gateways = PaymentGateway::all();
        return response()->json($gateways);
    }

    /**
     * Update a specific gateway configuration
     */
    public function update(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'config' => 'required|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $gateway->config = json_encode($request->config);
        $gateway->is_active = $request->get('is_active', false);
        $gateway->save();

        // 🔒 Add audit tracking later
        // log_activity('Updated payment gateway: ' . $gateway->name);

        return response()->json(['message' => 'Gateway updated successfully']);
    }

    /**
     * Create a new custom gateway
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'config' => 'required|array',
            'type' => 'required|string|in:online,crypto,manual,custom',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $gateway = PaymentGateway::create([
            'name' => $request->name,
            'type' => $request->type,
            'config' => json_encode($request->config),
            'is_active' => $request->get('is_active', false),
        ]);

        // log_activity('Created new payment gateway: ' . $gateway->name);

        return response()->json(['message' => 'Gateway created successfully']);
    }

    /**
     * Toggle activation status
     */
    public function toggle($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->is_active = !$gateway->is_active;
        $gateway->save();

        // log_activity('Toggled payment gateway: ' . $gateway->name);

        return response()->json(['message' => 'Gateway status updated']);
    }
}
