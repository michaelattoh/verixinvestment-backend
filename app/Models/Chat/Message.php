<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
    protected $table = 'chat_messages';

    protected $fillable = [
        'group_id',
        'sender_id',   // user id of sender
        'content',     // text message
        'type',        // text, image, voice, etc.
        'is_encrypted' // boolean, true if encrypted
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }
}
