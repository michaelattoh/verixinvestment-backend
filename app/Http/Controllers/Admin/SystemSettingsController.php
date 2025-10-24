<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    // list all settings or filter by group
    public function index(Request $request)
    {
        $group = $request->query('group');
        $query = SystemSetting::query();
        if ($group) $query->where('group', $group);
        $settings = $query->orderBy('group')->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'group' => $s->group,
                'key' => $s->key,
                'value' => $s->decoded_value,
                'type' => $s->type,
                'description' => $s->description,
            ];
        });
        return response()->json($settings);
    }

    // get single setting by group + key
    public function show($group, $key)
    {
        $s = SystemSetting::where('group', $group)->where('key', $key)->firstOrFail();
        return response()->json([
            'group' => $s->group,
            'key' => $s->key,
            'value' => $s->decoded_value,
            'type' => $s->type,
            'description' => $s->description,
        ]);
    }

    // update many settings at once (payload: [{group,key,value,type}])
    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.group' => 'required|string',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'nullable|string',
        ]);

        foreach ($data['settings'] as $s) {
            $type = $s['type'] ?? 'string';
            $value = $s['value'] ?? null;
            SystemSetting::set($s['group'].'.'.$s['key'], $value, $type);
        }

        return response()->json(['message' => 'Settings updated']);
    }

    // update single setting
    public function updateSingle(Request $request, $group, $key)
    {
        $this->validate($request, [
            'value' => 'nullable',
            'type' => 'nullable|string',
        ]);

        $type = $request->input('type', 'string');
        $value = $request->input('value', null);
        SystemSetting::set($group.'.'.$key, $value, $type);

        return response()->json(['message' => 'Setting updated']);
    }

    //file uploads
    public function uploadBranding(Request $request)
{
    $request->validate([
        'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:1024', // 1 MB
        'favicon' => 'nullable|mimes:png,ico|max:512', // 32x32 recommended
    ]);

    $data = [];

    if ($request->hasFile('logo')) {
        $path = $request->file('logo')->store('branding', 'public');
        SystemSetting::set('general.logo', $path, 'string');
        $data['logo'] = $path;
    }

    if ($request->hasFile('favicon')) {
        $path = $request->file('favicon')->store('branding', 'public');
        SystemSetting::set('general.favicon', $path, 'string');
        $data['favicon'] = $path;
    }

    return response()->json([
        'message' => 'Branding updated successfully',
        'data' => $data
    ]);
}
    //back to default settings
    public function resetGroup($group)
{
    \Artisan::call('db:seed', [
        '--class' => 'SystemSettingsSeeder'
    ]);

    return response()->json([
        'message' => ucfirst($group) . ' settings reset to default'
    ]);
}
    //notification settings
    
public function updateNotificationSettings(Request $request)
{
    $validated = $request->validate([
        // SMS
        'sms_provider' => 'nullable|string|in:custom,twilio,nexmo',
        'sms_from' => 'nullable|string',

        // Email
        'mail_driver' => 'nullable|string|in:smtp,sendmail,mailgun,ses',
        'mail_host' => 'nullable|string',
        'mail_port' => 'nullable|integer',
        'mail_username' => 'nullable|string',
        'mail_password' => 'nullable|string',
        'mail_from_address' => 'nullable|email',
        'mail_from_name' => 'nullable|string',

        // Push
        'push_enabled' => 'nullable|boolean',
        'push_provider' => 'nullable|string|in:firebase,onesignal',
        'push_api_key' => 'nullable|string',
        'push_base_url' => 'nullable|string',
    ]);

    foreach ($validated as $key => $value) {
        SystemSetting::set("notifications.$key", $value);
    }

    return response()->json([
        'message' => 'Notification settings updated successfully'
    ]);
}

public function sendTestEmail(Request $request)
{
    $validated = $request->validate([
        'to_email' => 'required|email',
        'message' => 'required|string',
    ]);

    try {
        \Mail::raw($validated['message'], function ($mail) use ($validated) {
            $mail->to($validated['to_email'])
                ->subject('Test Email from Verix Investment');
        });

        return response()->json(['message' => 'Test email sent successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to send test email: ' . $e->getMessage()], 500);
    }
}
public function updateCurrencySettings(Request $request)
{
    $validated = $request->validate([
        'detected_country' => 'nullable|string|size:2', // ISO country code
        'default_currency' => 'required|string|size:3', // ISO 4217 currency
        'currency_symbol' => 'required|string|max:5',
        'thousand_separator' => 'required|string|max:1',
        'decimal_separator' => 'required|string|max:1',
        'decimal_places' => 'required|integer|min:0|max:4',
        'symbol_on_left' => 'required|boolean',
        'space_between' => 'required|boolean',
    ]);

    foreach ($validated as $key => $value) {
        SystemSetting::set("currency.$key", $value);
    }

    return response()->json([
        'message' => 'Currency settings updated successfully'
    ]);
}

public function previewCurrencyFormat(Request $request)
{
    $validated = $request->validate([
        'amount' => 'required|numeric',
    ]);

    $symbol = SystemSetting::get('currency.currency_symbol');
    $thousand = SystemSetting::get('currency.thousand_separator');
    $decimal = SystemSetting::get('currency.decimal_separator');
    $places = SystemSetting::get('currency.decimal_places');
    $symbolLeft = SystemSetting::get('currency.symbol_on_left');
    $space = SystemSetting::get('currency.space_between');

    $formatted = number_format($validated['amount'], $places, $decimal, $thousand);

    if ($symbolLeft) {
        $preview = $symbol . ($space ? ' ' : '') . $formatted;
    } else {
        $preview = $formatted . ($space ? ' ' : '') . $symbol;
    }

    return response()->json([
        'preview' => $preview
    ]);
}
    //country IP
    public function getCurrencySettings(Request $request)
{
    $settings = SystemSetting::where('group', 'currency')->pluck('value', 'key');

    // Try to detect from user/vendor if logged in
    $user = $request->user();

    if ($user) {
        if ($user->role === 'vendor' && $user->vendor) {
            $settings['detected_country'] = $user->vendor->country_code ?? $settings['detected_country'];
        } else {
            $settings['detected_country'] = $user->country_code ?? $settings['detected_country'];
        }
    }

    return response()->json($settings);
}
}
