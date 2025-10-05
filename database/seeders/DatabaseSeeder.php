<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 2 Organiser accounts
        $org1 = User::factory()->create([
            'name' => 'Org One',
            'email' => 'org1@example.com',
            'password' => Hash::make('password'),
            'role' => 'organiser',
        ]);

        $org2 = User::factory()->create([
            'name' => 'Org Two',
            'email' => 'org2@example.com',
            'password' => Hash::make('password'),
            'role' => 'organiser',
        ]);

        // Create sample events -> organisers
        Event::create([
            'title' => 'Tech Conference 2026',
            'description' => 'Talks, workshops, and networking.',
            'event_date' => now()->addMonths(6),
            'location' => 'Brisbane Convention Centre',
            'organizer_id' => $org1->id,
        ]);

        Event::create([
            'title' => 'AI Meetup',
            'description' => 'Evening meetup with demos.',
            'event_date' => now()->addMonths(1)->setTime(18, 30),
            'location' => 'South Bank',
            'organizer_id' => $org2->id,
        ]);

        \App\Models\Event::factory(15)->create();
    }
}
