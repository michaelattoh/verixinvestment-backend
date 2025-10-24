<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Default roles
        $roles = [
            'user',
            'vendor',
            'admin',
            'super-admin',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Default permissions
        $permissions = [
            'view_dashboard',
            'manage_users',
            'manage_vendors',
            'manage_deposits',
            'manage_withdrawals',
            'view_transactions',
            'manage_transactions',
            'view_reports',
            'manage_settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Assign all permissions to super-admin role
        $superAdmin = Role::where('name', 'super-admin')->first();
        $allPermissions = Permission::all()->pluck('id')->toArray();
        $superAdmin->permissions()->sync($allPermissions);

        // Ensure admin has some basic permissions
        $admin = Role::where('name', 'admin')->first();
        $adminPerms = Permission::whereIn('name', [
            'view_dashboard',
            'manage_users',
            'manage_vendors',
            'manage_deposits',
            'manage_withdrawals',
            'view_transactions',
            'manage_transactions',
        ])->pluck('id')->toArray();
        $admin->permissions()->sync($adminPerms);
    }
}
