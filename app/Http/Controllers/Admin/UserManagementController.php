<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    // All Users with tabs (user, vendor, admin, super-admin)
    public function allUsers(Request $request)
    {
        $filter = $request->query('role', 'all');

        $query = User::with('roles');

        if ($filter !== 'all') {
            $query->whereHas('roles', function($q) use ($filter) {
                $q->where('name', $filter);
            });
        }

        $users = $query->get()->map(function ($user) {
            return [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'phone'    => $user->phone,
                'role'     => $user->roles->pluck('name')->first(),
                'avatar'   => $user->profile_picture,
            ];
        });

        return response()->json($users);
    }

        // List Roles
    public function roles()
    {
        return Role::all();
    }

    // List Permissions
    public function permissions()
    {
        return Permission::all();
    }

    // Assign permissions to role
    public function assignPermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
        ]);

        $role = Role::find($request->role_id);
        $role->permissions()->sync($request->permissions);

        return response()->json(['message' => 'Permissions updated successfully']);
    }

    // Assign a role to a user
    public function assignRole(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'role_id' => 'required|exists:roles,id',
    ]);

    $user = User::find($request->user_id);
    $user->roles()->sync([$request->role_id]); // replaces old role(s) with new one

    return response()->json(['message' => 'Role assigned successfully']);
}

}
