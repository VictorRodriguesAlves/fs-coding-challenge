<?php

namespace App\Jobs;

use App\Models\Message;
use App\Factories\Chat\ChannelFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Message $message)
    {}

    /**
     * Execute the job.
     */
    public function handle(ChannelFactory $channelFactory): void
    {
        $this->message->loadMissing('channel');

        try {
            $channelService = $channelFactory->make($this->message->channel);

            $channelService->send($this->message);

            $this->message->status = 'sent';

        } catch (\Exception $exception) {

            Log::error(
                "Failed to send message {$this->message->id}: " . $exception->getMessage(),
                ['message_id' => $this->message->id, 'exception' => $exception]
            );
            $this->message->status = 'failed';

        } finally {

            $this->message->save();
        }
    }
}
