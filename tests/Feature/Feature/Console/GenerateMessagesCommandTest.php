<?php

namespace Tests\Feature\Console;

use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use Database\Seeders\ChannelSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('messages:generate Command', function () {

    beforeEach(function () {
        $this->seed(UserSeeder::class);
        $this->seed(ChannelSeeder::class);

        $this->user = User::find(1);

        $this->contactA = Contact::factory()->create();
        $this->contactB = Contact::factory()->create();
        $this->user
            ->contacts()
            ->attach([$this->contactA->id, $this->contactB->id]);
    });

    it('generates messages for a specific contact', function () {
        $this->assertDatabaseCount('messages', 0);

        $this->artisan("messages:generate {$this->contactA->id} --count=20")
            ->assertSuccessful();

        $this->assertDatabaseCount('messages', 20);

        $messagesForOtherContacts = Message::query()
            ->where('contact_id', $this->contactB->id)
            ->count();
        expect($messagesForOtherContacts)->toBe(0);
    });

    it('generates messages randomly across all contacts', function () {
        $this->assertDatabaseCount('messages', 0);

        $this->artisan("messages:generate --count=50")
            ->assertSuccessful();

        $this->assertDatabaseCount('messages', 50);

        $messagesA = Message::query()
            ->where('contact_id', $this->contactA->id)
            ->count();
        $messagesB = Message::query()
            ->where('contact_id', $this->contactB->id)
            ->count();

        expect($messagesA)->toBeGreaterThan(0)
            ->and($messagesB)->toBeGreaterThan(0);
    });

    it('fails if specific contact is not found', function () {
        $this->artisan('messages:generate 999 --count=10')
            ->expectsOutput('Erro: Contato do ID 999 não encontrado ou não pertence ao usuário 1.')
            ->assertFailed();
    });

    it('fails if specific contact is not owned by user 1', function () {
        $unauthorizedContact = Contact::factory()->create();

        $this->artisan("messages:generate {$unauthorizedContact->id} --count=10")
            ->expectsOutput("Erro: Contato do ID {$unauthorizedContact->id} não encontrado ou não pertence ao usuário 1.")
            ->assertFailed();
    });

})->group('console');