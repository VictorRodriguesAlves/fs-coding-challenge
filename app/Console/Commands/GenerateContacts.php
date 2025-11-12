<?php

namespace App\Console\Commands;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\ContactIdentifier;
use App\Models\Message;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:generate
                            {--count=5 : O número total de contatos a serem gerados.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera novos contatos e os associa ao usuário logado (usuário 1).';
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = (int) $this->option('count');

        try {
            $user = User::findOrFail(1);
            $channel = Channel::query()
                ->where('name', 'whatsapp')
                ->firstOrFail();
        } catch (\Exception $e) {
            $this->error('Erro: Usuário 1 ou Canal "whatsapp" não encontrado.');
            $this->error('Por favor, rode o comando "php artisan db:seed" primeiro.');
            return Command::FAILURE;
        }

        $this->info("Gerando $count novos contatos para o usuário logado...");
        $bar = $this->output->createProgressBar($count);

        for ($i = 0; $i < $count; $i++) {

            $contact = Contact::factory()->create();
            $contact->users()->attach($user->id);

            ContactIdentifier::factory()->create([
                'contact_id' => $contact->id,
                'channel_id' => $channel->id,
                'identifier' => fake()->unique()->e164PhoneNumber(),
            ]);
            Message::factory()->create([
                'contact_id' => $contact->id,
                'channel_id' => $channel->id,
                'user_id' => null,
                'content' => fake()->sentence(6),
                'status' => 'sent',
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ $count novos contatos gerados com sucesso!");
        return Command::SUCCESS;
    }
}
