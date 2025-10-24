<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class GroupMember extends Model
{
    protected $table = 'chat_group_members';

    protected $fillable = [
        'group_id',
        'user_id',
        'is_admin', // optional: true if this member is admin
        'can_send_messages', // optional: for admin controls
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
