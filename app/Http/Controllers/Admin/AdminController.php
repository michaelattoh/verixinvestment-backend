<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

public function muteUser(Request $request)
{
    $groupId = $request->group_id;
    $userId = $request->user_id;

    // update DB or status
    broadcast(new UserMuted($groupId, $userId))->toOthers();

    return response()->json(['status' => 'user muted']);
}
