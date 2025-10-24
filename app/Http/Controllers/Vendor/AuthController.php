<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:vendors',
            'phone' => 'required|string',
            'country' => 'nullable|string',
            'password' => 'required|min:6'
        ]);

        $vendor = Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'password' => Hash::make($request->password),
            'role' => 'vendor'
        ]);

        $token = $vendor->createToken('vendor-token')->plainTextToken;

        return response()->json([
            'vendor' => $vendor,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $vendor = Vendor::where('email', $request->email)->first();

        if (! $vendor || ! Hash::check($request->password, $vendor->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $vendor->createToken('vendor-token')->plainTextToken;

        return response()->json([
            'vendor' => $vendor,
            'token' => $token
        ]);
    }
}

