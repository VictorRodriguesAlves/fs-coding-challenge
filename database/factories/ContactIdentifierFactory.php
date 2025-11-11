<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactIdentifier>
 */
class ContactIdentifierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'channel_id' => Channel::factory(),
            'identifier' => fake()->randomElement([fake()->e164PhoneNumber(), fake()->safeEmail()]),
        ];
    }
}
