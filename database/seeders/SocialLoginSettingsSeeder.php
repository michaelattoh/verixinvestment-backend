<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SocialLoginSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['group' => 'social_login', 'key' => 'google_client_id', 'value' => '', 'type' => 'string', 'description' => 'Google OAuth client ID'],
            ['group' => 'social_login', 'key' => 'google_client_secret', 'value' => '', 'type' => 'string', 'description' => 'Google OAuth client secret'],

            ['group' => 'social_login', 'key' => 'facebook_client_id', 'value' => '', 'type' => 'string', 'description' => 'Facebook OAuth client ID'],
            ['group' => 'social_login', 'key' => 'facebook_client_secret', 'value' => '', 'type' => 'string', 'description' => 'Facebook OAuth client secret'],

            ['group' => 'social_login', 'key' => 'apple_client_id', 'value' => '', 'type' => 'string', 'description' => 'Apple OAuth client ID'],
            ['group' => 'social_login', 'key' => 'apple_client_secret', 'value' => '', 'type' => 'string', 'description' => 'Apple OAuth client secret'],

            ['group' => 'social_login', 'key' => 'github_client_id', 'value' => '', 'type' => 'string', 'description' => 'GitHub OAuth client ID'],
            ['group' => 'social_login', 'key' => 'github_client_secret', 'value' => '', 'type' => 'string', 'description' => 'GitHub OAuth client secret'],

            ['group' => 'social_login', 'key' => 'social_login_enabled', 'value' => false, 'type' => 'boolean', 'description' => 'Enable or disable social login for users/vendors'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }
}
