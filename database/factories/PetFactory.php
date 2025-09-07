<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->firstName(),
            'species' => $this->faker->randomElement(['dog', 'cat', 'bird', 'fish', 'other']),
            'breed' => $this->faker->optional()->word(),
            'birth_date' => $this->faker->optional()->date(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'weight' => $this->faker->optional()->randomFloat(2, 0.5, 50),
            'notes' => $this->faker->optional()->sentence(),
            'photo' => $this->faker->optional()->imageUrl()
        ];
    }
}
