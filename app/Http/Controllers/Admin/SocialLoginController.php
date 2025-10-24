<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SocialLoginController extends Controller
{
    // GET /admin/social-login
    public function index()
    {
        $settings = SystemSetting::where('group', 'social_login')
            ->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    // POST /admin/social-login
    public function update(Request $request)
    {
        $data = $request->only([
            'google_client_id', 'google_client_secret',
            'facebook_client_id', 'facebook_client_secret',
            'apple_client_id', 'apple_client_secret',
            'github_client_id', 'github_client_secret',
            'social_login_enabled'
        ]);

        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(
                ['group' => 'social_login', 'key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Social login settings updated successfully.'
        ]);
    }
}
