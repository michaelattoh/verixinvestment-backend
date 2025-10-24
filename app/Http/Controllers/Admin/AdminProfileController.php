\<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminProfileController extends Controller
{
    public function show()
    {
        return response()->json(Auth::user());
    }

    public function update(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ]);

        $admin->update($request->only('full_name', 'email'));
        return response()->json(['message' => 'Profile updated successfully.']);
    }

    public function changePassword(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return response()->json(['error' => 'Current password is incorrect.'], 403);
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function logout()
    {
        $admin = Auth::user();
        $admin->tokens()->delete(); // For API token auth
        return response()->json(['message' => 'Logged out successfully.']);
    }
}
