<?php

namespace App\Chat\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Chat\Models\GroupMemberStatus;
use Illuminate\Support\Facades\Auth;

class GroupMemberStatusController extends Controller
{
    // Update typing status
    public function updateTyping(Request $request, $groupId)
    {
        $userId = Auth::id();

        $status = GroupMemberStatus::updateOrCreate(
            ['group_id' => $groupId, 'user_id' => $userId],
            ['is_typing' => $request->input('is_typing', false)]
        );

        return response()->json(['message' => 'Typing status updated', 'status' => $status]);
    }

    // Update mute status (admin only)
    public function updateMute(Request $request, $groupId)
    {
        // Optionally, you can check if Auth user is admin of the group here
        $userId = $request->input('user_id'); // user to mute/unmute

        $status = GroupMemberStatus::updateOrCreate(
            ['group_id' => $groupId, 'user_id' => $userId],
            ['is_muted' => $request->input('is_muted', false)]
        );

        return response()->json(['message' => 'Mute status updated', 'status' => $status]);
    }
}
