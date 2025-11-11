<?php

namespace Tests\Feature\Chat;

use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Chat Page (GET /chat)', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
    });

    it('loads chat page successfully', function () {
        $this->get(route('chat.index'))
            ->assertOk()
            ->assertInertia(fn($page) => $page->component('Chat/Index'));
    });

    it('returns the correct contacts prop', function () {
        $contacts = Contact::factory(3)->create();
        $this->user->contacts()->attach($contacts);

        $this->get(route('chat.index'))
            ->assertOk()
            ->assertInertia(fn($page) => $page->has('contacts', 3));
    });

    it('returns null messages when no contact is selected', function () {
        $this->get(route('chat.index'))
            ->assertOk()
            ->assertInertia(fn($page) => $page->where('messages', null));
    });

    it('returns messages when a contact is selected', function () {
        $contact = Contact::factory()->create();
        $this->user->contacts()->attach($contact);
        Message::factory(5)->create(['recipient_id' => $contact->id]);

        $this->get(route('chat.index', ['contact_id' => $contact->id]))
            ->assertOk()
            ->assertInertia(fn($page) => $page->has('messages', 5));
    });

    it('marks messages as read when contact is selected', function () {
        $contact = Contact::factory()->create();
        $this->user->contacts()->attach($contact);

        $messages = Message::factory(3)->create([
            'recipient_id' => $contact->id,
            'sender_id' => null,
            'read_at' => null
        ]);

        $this->get(route('chat.index', ['contact_id' => $contact->id]));

        foreach ($messages as $message) {
            $this->assertNotNull($message->fresh()->read_at);
        }
    });

    it('prevents user from accessing unauthorized contacts', function () {
        $otherUser = User::factory()->create();
        $unauthorizedContact = Contact::factory()->create();
        $otherUser->contacts()->attach($unauthorizedContact);

        $this->get(route('chat.index', ['contact_id' => $unauthorizedContact->id]))
            ->assertNotFound();
    });

});