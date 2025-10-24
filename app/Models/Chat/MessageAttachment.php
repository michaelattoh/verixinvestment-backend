<?php

namespace App\Chat\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $fillable = [
        'message_id', 'path', 'name', 'size', 'mime_type'
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
