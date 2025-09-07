<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 20, 100),
            'duration_minutes' => $this->faker->randomElement([30, 60, 90, 120]),
            'active' => true
        ];
    }
}
