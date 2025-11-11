<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenerateMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:generate 
                            {contact_id? : O ID do contato para o qual gerar mensagens.} 
                            {--count=100 : O número total de mensagens a serem geradas.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera mensagens para os contatos do usuário logado (usuário 1).';
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = (int) $this->option('count');
        $contactId = $this->argument('contact_id');

        try {
            $user = User::findOrFail(1);

            if ($contactId) {
                $this->info("Gerando $count mensagens para o Contato ID: $contactId (do Usuário 1)...");
                $this->generateForSpecificContact($user, $contactId, $count);
            }

            $this->info("Gerando $count mensagens aleatoriamente para os contatos do Usuário 1...");
            $this->generateRandomly($user, $count);

        } catch (\Exception $e) {
            $this->error("Erro: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("\n✅ Geração concluída!");
        return Command::SUCCESS;
    }

    private function generateForSpecificContact(User $user, int $contactId, int $count): void
    {
        try {
            $contact = $user->contacts()->findOrFail($contactId);
        } catch (ModelNotFoundException $e) {
            throw new \Exception("Contato ID $contactId não encontrado ou não pertence ao Usuário 1.");
        }

        $channelIds = Channel::query()
                        ->pluck('id');
        if ($channelIds->isEmpty()) {
            throw new \Exception("Nenhum canal encontrado. Rode o 'db:seed'.");
        }

        $bar = $this->output->createProgressBar($count);

        for ($i = 0; $i < $count; $i++) {
            $this->createFakeMessage($contact, $user, $channelIds);
            $bar->advance();
        }

        $bar->finish();
    }

    private function generateRandomly(User $user, int $count): void
    {
        $allChannels = Channel::query()
                        ->pluck('id');
        $contacts = $user->contacts;

        if ($contacts->isEmpty() || $allChannels->isEmpty()) {
            throw new \Exception('Usuário 1 não tem contatos ou não existem canais. Rode o db:seed.');
        }

        $bar = $this->output->createProgressBar($count);

        for ($i = 0; $i < $count; $i++) {
            $contact = $contacts->random();

            $this->createFakeMessage($contact, $user, $allChannels);
            $bar->advance();
        }

        $bar->finish();
    }

    private function createFakeMessage(Contact $contact, User $senderUser, Collection $channelIds): void
    {
        $isIncoming = (rand(0, 1) === 0);
        $messageData = [
            'recipient_id' => $contact->id,
            'channel_id' => $channelIds->random(),
            'content' => fake()->sentence(rand(3, 20)),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($isIncoming) {
            $messageData['sender_id'] = null;
            $messageData['status'] = 'sent';
            $messageData['read_at'] = null;
        } else {
            $messageData['sender_id'] = $senderUser->id;
            $messageData['status'] = 'sent';
            $messageData['read_at'] = null;
        }

        Message::query()->create($messageData);


        if (!$isIncoming) {
            Message::query()
                ->where('recipient_id', $contact->id)
                ->whereNull('sender_id')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }
}
