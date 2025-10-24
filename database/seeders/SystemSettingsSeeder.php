<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // --- General ---
        SystemSetting::updateOrCreate(['group'=>'general','key'=>'app_name'], ['value'=>'VeriX Investment','type'=>'string']);
        SystemSetting::updateOrCreate(['group'=>'general','key'=>'support_email'], ['value'=>'support@example.com','type'=>'string']);
        SystemSetting::updateOrCreate(['group'=>'general','key'=>'timezone'], ['value'=>'UTC','type'=>'string']);
        SystemSetting::updateOrCreate(['group'=>'general','key'=>'currency_default'], ['value'=>'USD','type'=>'string']);

        // Branding
        SystemSetting::firstOrCreate(
            ['group' => 'general', 'key' => 'logo'],
            ['value' => null, 'type' => 'string', 'description' => 'Path to site logo']
        );
        SystemSetting::firstOrCreate(
            ['group' => 'general', 'key' => 'favicon'],
            ['value' => null, 'type' => 'string', 'description' => 'Path to favicon']
        );
        SystemSetting::firstOrCreate(
            ['group' => 'general', 'key' => 'site_title'],
            ['value' => 'Verix Investment Platform', 'type' => 'string']
        );

        // Access Control
        SystemSetting::firstOrCreate(['group'=>'general','key'=>'user_signup_enabled'], ['value'=>true,'type'=>'boolean']);
        SystemSetting::firstOrCreate(['group'=>'general','key'=>'vendor_signup_enabled'], ['value'=>true,'type'=>'boolean']);

        // --- Notifications ---
        SystemSetting::updateOrCreate(['group'=>'notifications','key'=>'email_notifications'], ['value'=>'1','type'=>'boolean']);
        SystemSetting::updateOrCreate(['group'=>'notifications','key'=>'sms_notifications'], ['value'=>'0','type'=>'boolean']);
        SystemSetting::updateOrCreate(['group'=>'notifications','key'=>'push_notifications'], ['value'=>'0','type'=>'boolean']);

        // SMS Settings
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'sms_provider'], ['value'=>'custom','type'=>'string']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'sms_from'], ['value'=>null,'type'=>'string']);

        // Email Settings
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'mail_driver'], ['value'=>'smtp','type'=>'string']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'mail_host'], ['value'=>'smtp.mailtrap.io','type'=>'string']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'mail_port'], ['value'=>'2525','type'=>'integer']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'mail_username'], ['value'=>null,'type'=>'string']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'mail_password'], ['value'=>null,'type'=>'string']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'mail_from_address'], ['value'=>'noreply@verixinvestment.com','type'=>'string']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'mail_from_name'], ['value'=>'Verix Investment','type'=>'string']);

        // Push Notifications
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'push_enabled'], ['value'=>false,'type'=>'boolean']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'push_provider'], ['value'=>'firebase','type'=>'string']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'push_api_key'], ['value'=>null,'type'=>'string']);
        SystemSetting::firstOrCreate(['group'=>'notifications','key'=>'push_base_url'], ['value'=>null,'type'=>'string']);

        // --- Currency ---
        SystemSetting::updateOrCreate(['group'=>'currency','key'=>'available_currencies'], [
            'value' => json_encode(['USD','GHS','NGN','KES', 'GBP']), 'type' => 'json'
        ]);

        //currency details
        $settings = [
            ['group' => 'currency', 'key' => 'detected_country', 'value' => 'UK', 'type' => 'string'],
            ['group' => 'currency', 'key' => 'default_currency', 'value' => 'GBP', 'type' => 'string'],
            ['group' => 'currency', 'key' => 'currency_symbol', 'value' => '£', 'type' => 'string'],
            ['group' => 'currency', 'key' => 'thousand_separator', 'value' => ',', 'type' => 'string'],
            ['group' => 'currency', 'key' => 'decimal_separator', 'value' => '.', 'type' => 'string'],
            ['group' => 'currency', 'key' => 'decimal_places', 'value' => '2', 'type' => 'integer'],
            ['group' => 'currency', 'key' => 'symbol_on_left', 'value' => true, 'type' => 'boolean'],
            ['group' => 'currency', 'key' => 'space_between', 'value' => false, 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }

        // --- Investment Settings ---
        SystemSetting::updateOrCreate(['group'=>'investment','key'=>'daily_interest_rate'], ['value'=>'0.05','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'investment','key'=>'weekly_interest_rate'], ['value'=>'0.25','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'investment','key'=>'monthly_interest_rate'], ['value'=>'1.0','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'investment','key'=>'fixed_interest_rate'], ['value'=>'5.0','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'investment','key'=>'agricultural_interest_rate'], ['value'=>'3.0','type'=>'float']);

        // --- Deposit Settings ---
        SystemSetting::updateOrCreate(['group'=>'deposit','key'=>'min_amount'], ['value'=>'1','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'deposit','key'=>'max_amount'], ['value'=>'100000','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'deposit','key'=>'fee_percent'], ['value'=>'0','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'deposit','key'=>'auto_approve'], ['value'=>'0','type'=>'boolean']);

        // --- Withdrawal Settings ---
        SystemSetting::updateOrCreate(['group'=>'withdrawal','key'=>'min_amount'], ['value'=>'10','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'withdrawal','key'=>'max_amount'], ['value'=>'50000','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'withdrawal','key'=>'fee_percent'], ['value'=>'0','type'=>'float']);
        SystemSetting::updateOrCreate(['group'=>'withdrawal','key'=>'auto_approve'], ['value'=>'0','type'=>'boolean']);

        // --- Payment ---
        SystemSetting::updateOrCreate(['group'=>'payment','key'=>'gateway'], ['value'=>'stripe','type'=>'string']);
        SystemSetting::updateOrCreate(['group'=>'payment','key'=>'stripe_key'], ['value'=>'','type'=>'string']);
        SystemSetting::updateOrCreate(['group'=>'payment','key'=>'stripe_secret'], ['value'=>'','type'=>'string']);

        // --- Storage ---
        SystemSetting::updateOrCreate(['group'=>'storage','key'=>'disk'], ['value'=>'public','type'=>'string']);

        // --- Social Login ---
        SystemSetting::updateOrCreate(['group'=>'social','key'=>'google_client_id'], ['value'=>'','type'=>'string']);
        SystemSetting::updateOrCreate(['group'=>'social','key'=>'google_client_secret'], ['value'=>'','type'=>'string']);
    }
    $settings = [
        // --- Currency Settings ---
        ['group' => 'currency', 'key' => 'detected_country', 'value' => 'US', 'type' => 'string'],
        ['group' => 'currency', 'key' => 'default_currency', 'value' => 'USD', 'type' => 'string'],
        ['group' => 'currency', 'key' => 'currency_symbol', 'value' => '$', 'type' => 'string'],
        ['group' => 'currency', 'key' => 'thousand_separator', 'value' => ',', 'type' => 'string'],
        ['group' => 'currency', 'key' => 'decimal_separator', 'value' => '.', 'type' => 'string'],
        ['group' => 'currency', 'key' => 'decimal_places', 'value' => '2', 'type' => 'integer'],
        ['group' => 'currency', 'key' => 'symbol_on_left', 'value' => true, 'type' => 'boolean'],
        ['group' => 'currency', 'key' => 'space_between', 'value' => false, 'type' => 'boolean'],
    ];
}