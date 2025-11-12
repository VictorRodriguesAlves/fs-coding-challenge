<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastMessage' => $this->whenLoaded('latestMessage', $this->latestMessage?->content),
            'lastMessageTime' => $this->whenLoaded('latestMessage', $this->latestMessage?->created_at->diffForHumans()),
            'unreadCount' => $this->when(isset($this->unread_messages_count), $this->unread_messages_count),
            'online' => false,
        ];
    }
}
