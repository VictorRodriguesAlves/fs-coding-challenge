<?php

namespace App\Services\Channels;

use App\Interfaces\Chat\ChannelInterface;
use App\Models\Message;

class MessengerChannel implements ChannelInterface
{
    public function send(Message $message): void
    {
        sleep(rand(1, 3));

        if (rand(1, 10) === 1) {
            throw new \Exception('Simulated send failure for Messenger');
        }
    }
}