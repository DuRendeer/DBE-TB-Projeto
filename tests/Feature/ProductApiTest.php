<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_products_list()
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id, 'active' => true]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                ->assertJsonCount(3);
    }

    public function test_can_create_product()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $productData = [
            'category_id' => $category->id,
            'name' => 'Premium Dog Food',
            'description' => 'High quality dog food for all breeds',
            'price' => 49.99,
            'stock_quantity' => 100,
            'sku' => 'DOG-FOOD-001',
            'active' => true
        ];

        $response = $this->actingAs($user)
                         ->postJson('/api/products', $productData);

        $response->assertStatus(201)
                ->assertJsonFragment(['name' => 'Premium Dog Food']);

        $this->assertDatabaseHas('products', ['sku' => 'DOG-FOOD-001']);
    }

    public function test_can_update_product()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $updateData = ['name' => 'Updated Product Name'];

        $response = $this->actingAs($user)
                         ->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonFragment(['name' => 'Updated Product Name']);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name'
        ]);
    }
}