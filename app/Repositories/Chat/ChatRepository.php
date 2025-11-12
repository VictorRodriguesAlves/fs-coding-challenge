<?php

namespace App\Repositories\Chat;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository
{

    public function getAllChannels(): Collection
    {
        return Channel::all();
    }

    public function getContactsForUser(User $user, ?string $searchQuery = null): Collection
    {
        $query = $user->contacts()
            ->with('latestMessage')
            ->withCount('unreadMessages');

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('contacts.name', 'LIKE', "%{$searchQuery}%")
                    ->orWhereHas('latestMessage', function ($subQuery) use ($searchQuery) {
                        $subQuery->where('content', 'LIKE', "%{$searchQuery}%");
                    });
            });
        }

        return $query->get();
    }

    public function findContactForUser(User $user, int $contactId): Contact|Collection
    {
        return $user->contacts()->findOrFail($contactId);
    }

    public function getMessagesForContact(Contact $contact): Paginator
    {
        return $contact->messages()
            ->orderBy('created_at', 'desc')
            ->simplePaginate(20);
    }
}