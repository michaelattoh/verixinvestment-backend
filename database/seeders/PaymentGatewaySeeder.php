<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'PayPal',
                'type' => 'online',
                'config' => json_encode([
                    'client_id' => '',
                    'client_secret' => '',
                    'mode' => 'sandbox' // or live
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Stripe',
                'type' => 'online',
                'config' => json_encode([
                    'public_key' => '',
                    'secret_key' => '',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Razorpay',
                'type' => 'online',
                'config' => json_encode([
                    'key_id' => '',
                    'key_secret' => '',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Paystack',
                'type' => 'online',
                'config' => json_encode([
                    'public_key' => '',
                    'secret_key' => '',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Flutterwave',
                'type' => 'online',
                'config' => json_encode([
                    'public_key' => '',
                    'secret_key' => '',
                    'encryption_key' => '',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Coinbase Commerce',
                'type' => 'crypto',
                'config' => json_encode([
                    'api_key' => '',
                    'webhook_secret' => '',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'BitPay',
                'type' => 'crypto',
                'config' => json_encode([
                    'token' => '',
                    'base_url' => 'https://bitpay.com',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'USDT (TRC20/ERC20)',
                'type' => 'crypto',
                'config' => json_encode([
                    'wallet_address' => '',
                    'network' => 'TRC20',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Bank Transfer',
                'type' => 'manual',
                'config' => json_encode([
                    'bank_name' => '',
                    'account_name' => '',
                    'account_number' => '',
                    'note' => '',
                ]),
                'is_active' => false,
            ],
            [
                'name' => 'Custom Gateway',
                'type' => 'custom',
                'config' => json_encode([
                    'custom_name' => '',
                    'api_key' => '',
                    'secret_key' => '',
                    'base_url' => '',
                    'callback_url' => '',
                ]),
                'is_active' => false,
            ],
            
        ];

        DB::table('payment_gateways')->insert($gateways);
    }
}
