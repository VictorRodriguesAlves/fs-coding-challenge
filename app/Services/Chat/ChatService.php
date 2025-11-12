<?php

namespace App\Services\Chat;

use App\Models\Contact;
use App\Models\User;
use App\Repositories\Chat\ChatRepository;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\ContactResource;

class ChatService
{
    public function __construct(
        private ChatRepository $chatRepository
    ) {}

    public function getPageData(User $user, Contact $selectedContact = null, ?string $searchQuery = null): array
    {
        $channels = $this->chatRepository->getAllChannels();
        $contacts = $this->chatRepository->getContactsForUser($user, $searchQuery);
        $messagesPaginator = null;

        if ($selectedContact) {
            $this->validateContactOwnership($user, $selectedContact);

            $this->markMessagesAsRead($selectedContact);

            $messagesPaginator = $this->chatRepository->getMessagesForContact($selectedContact);

            $messagesPaginator->getCollection()->transform(function ($message) use ($user) {
                return $this->transformMessage($message, $user);
            });
        }

        return [
            'contacts' => ContactResource::collection($contacts),
            'messages' => $messagesPaginator,
            'selectedContact' => $selectedContact,
            'channels' => ChannelResource::collection($channels),
        ];
    }

    private function validateContactOwnership(User $user, Contact $contact): void
    {
        try {
            $this->chatRepository->findContactForUser($user, $contact->id);
        } catch (\Exception $e) {
            throw new AuthorizationException("Você não tem permissão para ver este contato.");
        }
    }

    private function markMessagesAsRead(Contact $contact): void
    {
        $contact->unreadMessages()->update(['read_at' => now()]);
    }
    private function transformMessage($message, User $user): array
    {
        return [
            'id' => $message->id,
            'text' => $message->content,
            'sender' => $message->user_id === $user->id ? 'me' : 'contact',
            'time' => $message->created_at->format('H:i'),
            'date' => $message->created_at->toIso8601String(),
            'status' => $message->status,
        ];
    }

}