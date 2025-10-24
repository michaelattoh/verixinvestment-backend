<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SocialLoginSettingsController extends Controller
{
    public function index()
    {
        // Fetch all social login settings
        $settings = SystemSetting::where('group', 'social_login')->get();
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            SystemSetting::updateOrCreate(
                ['group' => 'social_login', 'key' => $key],
                ['value' => $value]
            );
        }

        return response()->json(['message' => 'Social login settings updated successfully.']);
    }
}
