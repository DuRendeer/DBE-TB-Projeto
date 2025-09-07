<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Pet;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'pet_id' => Pet::factory(),
            'service_id' => Service::factory(),
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'in_progress', 'completed', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
            'total_price' => $this->faker->randomFloat(2, 20, 100)
        ];
    }
}
