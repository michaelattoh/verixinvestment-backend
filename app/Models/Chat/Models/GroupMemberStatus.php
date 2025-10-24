<?php

namespace App\Chat\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMemberStatus extends Model
{
    protected $fillable = ['group_id', 'user_id', 'is_muted', 'is_typing'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
