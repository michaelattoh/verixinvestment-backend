<?php

namespace App\Chat\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Chat\Models\Message;
use App\Chat\Models\MessageAttachment;

class MessageController extends Controller
{
    // Fetch messages for a group
    public function index(Request $request, $groupId)
    {
        $messages = Message::with('attachments', 'sender')
            ->where('group_id', $groupId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Send a new message
    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'content' => 'nullable|string',
            'attachments' => 'array', // optional file array
        ]);

        $message = Message::create([
            'group_id' => $request->group_id,
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        // Handle attachments if any
        if ($request->has('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('chat_attachments', 'public');
                MessageAttachment::create([
                    'message_id' => $message->id,
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                ]);
            }
        }

        return response()->json(['message' => 'Message sent', 'data' => $message]);
    }

    // Mark message as read (optional)
    public function markRead($id)
    {
        $message = Message::findOrFail($id);
        $message->read_at = now();
        $message->save();

        return response()->json(['message' => 'Message marked as read']);
    }
    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'group_id' => $request->group_id,
            'sender_id' => $request->user()->id,
            'message' => $request->message,
        ]);

    broadcast(new MessageSent($message))->toOthers();

    return response()->json($message, 201);
    }

    public function typing(Request $request)
    {
        broadcast(new UserTyping($request->group_id, $request->user()->id))->toOthers();
        return response()->json(['status' => 'typing broadcasted']);
    }
}
