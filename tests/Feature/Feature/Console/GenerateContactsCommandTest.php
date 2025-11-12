<?php

namespace Tests\Feature\Console;

use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use Database\Seeders\ChannelSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('contacts:generate Command', function () {

    it('fails gracefully if dependencies are missing', function () {
        $this->artisan('contacts:generate')
            ->expectsOutput('Erro: Usuário 1 ou Canal "whatsapp" não encontrado.')
            ->expectsOutput('Por favor, rode o comando "php artisan db:seed" primeiro.')
            ->assertFailed();
    });

    describe('when dependencies exist', function () {

        beforeEach(function () {
            $this->seed(UserSeeder::class);
            $this->seed(ChannelSeeder::class);
        });

        it('generates new contacts with all relations', function () {
            $this->artisan('contacts:generate --count=6')
                ->expectsOutput('Gerando 6 novos contatos para o usuário logado...')
                ->assertSuccessful();

            $this->assertDatabaseCount('contacts', 6);
            $this->assertDatabaseCount('contact_user', 6);
            $this->assertDatabaseCount('contacts_identifiers', 6);
            $this->assertDatabaseCount('messages', 6);
        });

        it('generates 5 contacts by default', function () {
            $this->artisan('contacts:generate')
                ->assertSuccessful();

            $this->assertDatabaseCount('contacts', 5);
            $this->assertDatabaseCount('messages', 5);
        });

    });

})->group('console');