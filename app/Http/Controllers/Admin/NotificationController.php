<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->paginate(20);
        return response()->json($notifications);
    }

    public function markRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read.']);
    }
}
