<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'chat_groups'; // the table name in your database
    protected $fillable = [
        'name',
        'admin_id', // the user who created/admins the group
        'description',
        // any other fields
    ];

    // Relationships, if needed
    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_group_members', 'group_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'group_id');
    }
}
