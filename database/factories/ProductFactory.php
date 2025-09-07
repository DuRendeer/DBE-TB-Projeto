<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'sku' => $this->faker->unique()->bothify('SKU-###-???'),
            'images' => [$this->faker->imageUrl(), $this->faker->imageUrl()],
            'weight' => $this->faker->randomFloat(2, 0.1, 10),
            'dimensions' => $this->faker->bothify('##x##x## cm'),
            'active' => true
        ];
    }
}
