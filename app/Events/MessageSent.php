<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $chat;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Chat $chat
     */
    public function __construct(Chat $chat)
    {
        $this->chat = $chat;

        \Log::info('MessageSent Event Constructed:', [
            'session_id' => $chat->session_id,
            'message' => $chat->message,
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        \Log::info('Broadcasting to channel:', [
            'channel' => "chat.{$this->chat->session_id}",
        ]);

        return new Channel("chat.{$this->chat->session_id}");
    }

    /**
     * Additional broadcast data to send with the event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $data = [
            'message' => $this->chat->message,
            'user_id' => $this->chat->user_id,
            'created_at' => $this->chat->created_at->toDateTimeString(),
        ];

        \Log::info('Broadcasting Data:', $data);

        return $data;
    }
}
