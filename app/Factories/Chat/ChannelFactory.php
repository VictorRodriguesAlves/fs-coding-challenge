<?php

namespace App\Factories\Chat;

use App\Interfaces\Chat\ChannelInterface;
use App\Models\Channel;
use App\Services\Channels\EmailChannel;
use App\Services\Channels\MessengerChannel;
use App\Services\Channels\WhatsAppChannel;
use InvalidArgumentException;

class ChannelFactory
{
    public function make(Channel $channel): ChannelInterface
    {
        return match ($channel->name) {
            'WhatsApp' => new WhatsAppChannel(),
            'Messenger' => new MessengerChannel(),
            'Email' => new EmailChannel(),
            default => throw new InvalidArgumentException("Canal '{$channel->name}' não suportado."),
        };
    }
}