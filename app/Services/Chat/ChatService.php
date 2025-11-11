<?php

namespace App\Services\Chat;

use App\Models\Contact;
use App\Models\User;
use App\Repositories\Chat\ChatRepository;
use Illuminate\Support\Collection;

class ChatService
{
    public function __construct(
        private ChatRepository $chatRepository
    ) {}

    public function getPageData(User $user, int $selectedContactId = null): array
    {
        $channels = $this->chatRepository->getAllChannels();
        $contacts = $this->chatRepository->getContactsForUser($user);

        $messagesPaginator = null;
        $selectedContact = null;

        if ($selectedContactId) {
            $selectedContact = $this->chatRepository->findContactForUser($user, $selectedContactId);

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

    private function transformChannels(Collection $channels): Collection
    {
        return $channels->map(fn ($channel) => [
            'id' => $channel->id,
            'name' => $channel->name,
        ]);
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
}