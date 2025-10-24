<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class AdminForgotPasswordController extends Controller
{
    // Step 1: Request password reset
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Check if email belongs to an admin
        $admin = \App\Models\User::where('email', $request->email)
            ->where('role', 'admin')
            ->first();

        if (!$admin) {
            return response()->json(['error' => 'Admin with this email does not exist.'], 404);
        }

        // Generate token and store in password_resets
        $token = Str::random(60);
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );

        // Send reset email (frontend handles URL)
        Mail::to($request->email)->send(new \App\Mail\AdminResetPasswordMail($token));

        return response()->json(['message' => 'Password reset link sent to your email.']);
    }

    // Step 2: Reset password
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['error' => 'Invalid or expired token.'], 400);
        }

        $admin = \App\Models\User::where('email', $request->email)
            ->where('role', 'admin')
            ->first();

        if (!$admin) {
            return response()->json(['error' => 'Admin not found.'], 404);
        }

        $admin->password = bcrypt($request->new_password);
        $admin->save();

        // Delete used token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
