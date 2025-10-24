<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroupMember;

class GroupMemberController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
            'is_admin' => 'boolean',
        ]);

        $member = GroupMember::updateOrCreate(
            ['group_id' => $request->group_id, 'user_id' => $request->user_id],
            ['is_admin' => $request->is_admin ?? false]
        );

        return response()->json($member, 201);
    }

    public function updatePermissions(Request $request, GroupMember $member)
    {
        $request->validate([
            'can_send_messages' => 'boolean',
        ]);

        $member->update(['can_send_messages' => $request->can_send_messages]);
        return response()->json($member);
    }

    public function destroy(GroupMember $member)
    {
        $member->delete();
        return response()->json(['message' => 'Member removed']);
    }
}
