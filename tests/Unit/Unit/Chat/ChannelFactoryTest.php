<?php

namespace Tests\Unit\Chat;

use App\Factories\Chat\ChannelFactory;
use App\Models\Channel;
use App\Services\Channels\EmailChannel;
use App\Services\Channels\MessengerChannel;
use App\Services\Channels\WhatsAppChannel;


describe('Channel Factory', function () {

    it('resolves the correct strategy for supported channels', function (string $channelName, string $expectedClass) {

        $channel = new Channel(['name' => $channelName]);
        $factory = new ChannelFactory();

        $service = $factory->make($channel);

        expect($service)
            ->toBeInstanceOf($expectedClass);

    })->with([
        'whatsapp' => ['WhatsApp', WhatsAppChannel::class],
        'email' => ['Email', EmailChannel::class],
        'messenger' => ['Messenger', MessengerChannel::class],
    ]);


    it('throws an exception for an unsupported channel', function () {
        $channel = new Channel(['name' => 'unsupported']);
        $factory = new ChannelFactory();

        expect(fn() => $factory->make($channel))
            ->toThrow(\InvalidArgumentException::class);
    });

});