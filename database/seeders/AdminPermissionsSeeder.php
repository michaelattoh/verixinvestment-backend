<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class AdminPermissionsSeeder extends Seeder
{
    public function run()
    {
        // 1. Create the permission (only checks 'name' since 'description' column doesn't exist)
        $permission = Permission::firstOrCreate([
            'name' => 'manage_social_login',
        ]);

        // Optionally set description if your model supports it
        if (property_exists($permission, 'description')) {
            $permission->description = 'Access and manage social login settings';
            $permission->save();
        }

        // 2. Assign permission to admin and super admin roles
        $roles = Role::whereIn('name', ['admin', 'super_admin'])->get();

        foreach ($roles as $role) {
            if (!$role->permissions->contains($permission->id)) {
                $role->permissions()->attach($permission->id);
            }
        }

        $this->command->info('Permission "manage_social_login" added and assigned to admin/super_admin roles.');
    }
}
