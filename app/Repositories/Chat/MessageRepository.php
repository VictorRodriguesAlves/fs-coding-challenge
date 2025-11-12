<?php

namespace App\Repositories\Chat;

use App\Models\Message;
use App\Models\User;

class MessageRepository
{

    public function createMessage(array $validatedData, User $sender): Message
    {
        return Message::query()
                ->create([
                    'content' => $validatedData['content'],
                    'channel_id' => $validatedData['channel_id'],
                    'contact_id' => $validatedData['contact_id'],
                    'user_id' => $sender->id,
                    'status' => 'sending',
                ]);
    }
}