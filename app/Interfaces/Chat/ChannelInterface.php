<?php

namespace App\Interfaces\Chat;

use App\Models\Message;

interface ChannelInterface
{
    public function send(Message $message): void;
}