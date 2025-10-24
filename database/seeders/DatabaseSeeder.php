<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // so you can log in
            'role' => 'admin', 
        ]);

        // Run additional seeders
        $this->call([
            RolesAndPermissionsSeeder::class, 
            SystemSettingsSeeder::class,
            PaymentGatewaySeeder::class,
        ]);
    }
}
