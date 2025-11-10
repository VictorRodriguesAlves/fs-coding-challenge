<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Channel::query()
            ->firstOrCreate(['name' => 'WhatsApp']);
        Channel::query()
            ->firstOrCreate(['name' => 'Messenger']);
        Channel::query()
            ->firstOrCreate(['name' => 'Email']);
    }
}
