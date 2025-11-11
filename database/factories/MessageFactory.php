<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipient_id' => Contact::factory(),
            'channel_id' => Channel::factory(),
            'sender_id' => User::factory(),
            'content' => fake()->sentence(),
            'status' => fake()->randomElement(['sending', 'sent', 'failed']),
            'read_at' => fake()->randomElement([null, now()]),
        ];
    }
}
