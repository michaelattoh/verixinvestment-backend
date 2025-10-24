<?php

namespace App\Chat\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Chat\Models\Group;
use App\Chat\Models\GroupMember;

class GroupController extends Controller
{
    // List all groups for the logged-in user
    public function index(Request $request)
    {
        $groups = Group::with('members')->whereHas('members', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->get();

        return response()->json($groups);
    }

    // Create a new group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'array', // optional array of user IDs
        ]);

        $group = Group::create(['name' => $request->name]);

        // Add creator as member & admin
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $request->user()->id,
            'is_admin' => true,
            'can_send_messages' => true,
        ]);

        // Add additional members if provided
        if ($request->filled('members')) {
            foreach ($request->members as $userId) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $userId,
                    'is_admin' => false,
                    'can_send_messages' => true,
                ]);
            }
        }

        return response()->json(['message' => 'Group created successfully', 'group' => $group]);
    }

    // View group details
    public function show($id)
    {
        $group = Group::with('members.user')->findOrFail($id);
        return response()->json($group);
    }

    // Update group name
    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $request->validate(['name' => 'required|string|max:255']);
        $group->update(['name' => $request->name]);

        return response()->json(['message' => 'Group updated successfully', 'group' => $group]);
    }

    // Delete group
    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json(['message' => 'Group deleted successfully']);
    }
}
