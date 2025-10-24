<?php

namespace App\Chat\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserTyping implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $groupId;
    public $userId;

    public function __construct($groupId, $userId)
    {
        $this->groupId = $groupId;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new Channel('group.' . $this->groupId);
    }

    public function broadcastAs()
    {
        return 'user.typing';
    }
}
