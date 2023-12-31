<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{ 
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $conversation;
    public $typing;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($conversation,$typing)
    {
        $this->conversation=$conversation;
        $this->typing=$typing;
        $this->dontBroadcastToCurrentUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
         return new PrivateChannel('chat.'.$this->conversation);
    }
}
