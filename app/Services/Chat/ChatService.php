<?php

namespace App\Services\Chat;

use App\Models\Contact;
use App\Models\User;
use App\Repositories\Chat\ChatRepository;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;

class ChatService
{
    public function __construct(
        private ChatRepository $chatRepository
    ) {}

    public function getPageData(User $user, Contact $selectedContact = null): array
    {
        $channels = $this->chatRepository->getAllChannels();
        $contacts = $this->chatRepository->getContactsForUser($user);

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
            'contacts' => $this->transformContacts($contacts),
            'messages' => $messagesPaginator,
            'selectedContact' => $selectedContact,
            'channels' => $this->transformChannels($channels),
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

    private function transformContacts(Collection $contacts): Collection
    {
        return $contacts->map(function ($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->name,
                'lastMessage' => $contact->latestMessage?->content,
                'lastMessageTime' => $contact->latestMessage?->created_at->diffForHumans(),
                'unreadCount' => $contact->unread_messages_count,
                'online' => false,
            ];
        });
    }

    private function transformMessage($message, User $user): array
    {
        return [
            'id' => $message->id,
            'text' => $message->content,
            'sender' => $message->sender_id === $user->id ? 'me' : 'contact',
            'time' => $message->created_at->format('H:i'),
            'date' => $message->created_at->toDateString(),
            'status' => $message->status,
        ];
    }

    private function transformChannels(Collection $channels): Collection
    {
        return $channels->map(fn ($channel) => [
            'id' => $channel->id,
            'name' => $channel->name,
        ]);
    }
}