<?php

namespace App\Chat\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Chat\Models\MessageAttachment;
use App\Chat\Models\Message;
use App\Services\StorageManager;
use Illuminate\Support\Facades\Validator;

class MessageAttachmentController extends Controller
{
    // Upload an attachment
    public function store(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);

        // Check permission: user must belong to the group
        $userId = $request->user()->id;
        if (!$message->group->members->contains($userId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $disk = StorageManager::getActiveDisk();
        $path = $file->store('attachments', ['disk' => config('filesystems.default')]);

        $attachment = MessageAttachment::create([
            'message_id' => $message->id,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
        ]);

        return response()->json(['attachment' => $attachment], 201);
    }

    // Download an attachment
    public function download($id)
    {
        $attachment = MessageAttachment::findOrFail($id);

        // Optional: check if user belongs to the group
        // ...

        $disk = StorageManager::getActiveDisk();
        return $disk->download($attachment->path, $attachment->name);
    }

    // Delete an attachment
    public function destroy(Request $request, $id)
    {
        $attachment = MessageAttachment::findOrFail($id);

        // Only sender or group admin can delete
        $user = $request->user();
        if ($attachment->message->sender_id !== $user->id && !$attachment->message->group->isAdmin($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $disk = StorageManager::getActiveDisk();
        $disk->delete($attachment->path);
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully']);
    }
}
