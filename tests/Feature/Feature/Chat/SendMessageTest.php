<?php


namespace Tests\Feature\Chat;

use App\Factories\Chat\ChannelFactory;
use App\Interfaces\Chat\ChannelInterface;
use App\Jobs\SendMessageJob;
use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use App\Services\Chat\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Mockery;

uses(RefreshDatabase::class);

describe('Send Message Process', function () {

    beforeEach(function () {
        Queue::fake();
        Log::spy();

        $this->user = User::factory()->create();
        $this->contact = Contact::factory()->create();
        $this->channel = Channel::factory()->create(['name' => 'WhatsApp']);
        $this->user->contacts()->attach($this->contact);
    });

    it('creates a message with sending status and dispatches job', function () {
        $service = $this->app->make(MessageService::class);
        $data = [
            'contact_id' => $this->contact->id,
            'channel_id' => $this->channel->id,
            'content' => 'Hello test message'
        ];

        $service->sendMessage($data, $this->user);

        Queue::assertPushed(SendMessageJob::class);
        $this->assertDatabaseHas('messages', [
            'content' => 'Hello test message',
            'status' => 'sending',
            'sender_id' => $this->user->id,
            'recipient_id' => $this->contact->id
        ]);
    });

    it('updates message status to sent on successful job', function () {

        $mockService = Mockery::mock(ChannelInterface::class);
        $mockService
            ->shouldReceive('send')
            ->once()
            ->andReturnNull();

        $this->mock(ChannelFactory::class, function ($mock) use ($mockService) {
            $mock
                ->shouldReceive('make')
                ->once()
                ->andReturn($mockService);
        });

        $message = Message::factory()->create([
            'status' => 'sending',
            'channel_id' => $this->channel->id,
            'recipient_id' => $this->contact->id,
        ]);

        (new SendMessageJob($message))->handle($this->app->make(ChannelFactory::class));

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'sent'
        ]);
    });

    it('updates message status to failed on job failure', function () {

        $mockService = Mockery::mock(ChannelInterface::class);
        $mockService
            ->shouldReceive('send')
            ->once()
            ->andThrow(new \Exception('Simulated send failure'));

        $this->mock(ChannelFactory::class, function ($mock) use ($mockService) {
            $mock
                ->shouldReceive('make')
                ->once()
                ->andReturn($mockService);
        });


        $message = Message::factory()->create([
            'status' => 'sending',
            'channel_id' => $this->channel->id,
            'recipient_id' => $this->contact->id,
        ]);

        (new SendMessageJob($message))->handle($this->app->make(ChannelFactory::class));

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'failed'
        ]);

    });

});