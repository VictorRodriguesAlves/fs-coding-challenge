<?php

namespace App\Services\Chat;

use App\Jobs\SendMessageJob;
use App\Models\User;
use App\Repositories\Chat\MessageRepository;

class MessageService
{

    public function __construct(
        private MessageRepository $messageRepository
    ) {}

    public function sendMessage(array $validatedData, User $sender): void
    {
        $message = $this->messageRepository->createMessage($validatedData, $sender);
        SendMessageJob::dispatch($message);
    }
}