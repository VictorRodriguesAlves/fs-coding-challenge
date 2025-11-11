<?php

namespace Tests\Feature\Chat;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Message Store (POST /messages)', function () {

    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->channel = Channel::factory()->create();
        $this->contact = Contact::factory()->create();

        $this->user->contacts()->attach($this->contact);

        $this->validData = [
            'content' => 'This is a valid message',
            'contact_id' => $this->contact->id,
            'channel_id' => $this->channel->id,
        ];
    });

    it('passes validation and authorization with correct data', function () {
        $this->post(route('messages.store'), $this->validData)
            ->assertSessionHasNoErrors()
            ->assertRedirect();
    });

    it('fails validation if content is empty', function () {
        $this->validData['content'] = '';

        $this->post(route('messages.store'), $this->validData)
            ->assertSessionHasErrors('content');
    });

    it('fails validation if channel_id does not exist', function () {
        $this->validData['channel_id'] = 999;

        $this->post(route('messages.store'), $this->validData)
            ->assertSessionHasErrors('channel_id');
    });

    it('fails validation if contact_id does not exist', function () {
        $this->validData['contact_id'] = 999;

        $this->post(route('messages.store'), $this->validData)
            ->assertSessionHasErrors('contact_id');
    });

    it('prevents user from sending message to an unauthorized contact', function () {
        $unauthorizedContact = Contact::factory()->create();

        $this->validData['contact_id'] = $unauthorizedContact->id;

        $this->post(route('messages.store'), $this->validData)
            ->assertForbidden();
    });

});