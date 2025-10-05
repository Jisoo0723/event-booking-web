<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'event_date' => now()->addDays(rand(1, 60)),
            'location' => $this->faker->city(),
            'capacity' => rand(20, 200),
            'organizer_id' => 1, // 미리 만든 organiser 계정 (예: admin)
        ];
    }
}
