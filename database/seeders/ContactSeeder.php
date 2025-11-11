<?php

namespace Database\Seeders;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\ContactIdentifier;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()
            ->where('email', 'user@example.com')
            ->first();

        $channel = Channel::query()
            ->where('name', 'whatsapp')
            ->first();

        if (!$user || !$channel) {
            $this->command->error('UserSeeder e ChannelSeeder precisam ser executados primeiro!');
            return;
        }


        $contact = Contact::query()
            ->firstOrCreate(
                ['name' => 'Cliente Teste'],
                []
            );

        $user->contacts()->syncWithoutDetaching([$contact->id]);

        ContactIdentifier::query()
            ->firstOrCreate(
                [
                    'contact_id' => $contact->id,
                    'channel_id' => $channel->id
                ],
                ['identifier' => '+5511999998888']
            );

        Message::query()
            ->firstOrCreate(
                [
                    'recipient_id' => $contact->id,
                    'sender_id' => null,
                    'content' => 'Olá! Esta é uma mensagem de teste para começar.'
                ],
                [
                    'channel_id' => $channel->id,
                    'status' => 'sent',
                    'read_at' => null
                ]
            );
    }
}
