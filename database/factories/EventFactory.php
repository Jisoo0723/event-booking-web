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
        $cats = ['Art','Business','Fashion','Film','Food & Drink','Music','Sports','Tech'];
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'event_date' => now()->addDays(rand(1, 120)),
            'location' => $this->faker->city(),
            'capacity' => rand(20, 200),
            'category' => $cats[array_rand($cats)],
            'image' => null, // 필요 시 파일명
            'organizer_id' => 1, // 미리 만든 organiser 계정
        ];
    }
}
