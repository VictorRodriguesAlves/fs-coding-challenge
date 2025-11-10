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
        $atendente = User::query()
            ->where('email', 'atendente@example.com')
            ->first();

        $channelWA = Channel::query()
            ->where('name', 'WhatsApp')
            ->first();

        if (!$atendente || !$channelWA) {
            $this->command->error('UserSeeder e ChannelSeeder precisam ser executados primeiro!');
            return;
        }


        $contact = Contact::query()
            ->firstOrCreate(
                ['name' => 'Cliente Teste'],
                []
            );

        $atendente->contacts()->syncWithoutDetaching([$contact->id]);

        ContactIdentifier::query()
            ->firstOrCreate(
                [
                    'contact_id' => $contact->id,
                    'channel_id' => $channelWA->id
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
                    'channel_id' => $channelWA->id,
                    'status' => 'sent',
                    'read_at' => null
                ]
            );
    }
}
