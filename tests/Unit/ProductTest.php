<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_price_is_cast_to_decimal()
    {
        $product = Product::factory()->create(['price' => 29.99]);

        $this->assertEquals('29.99', $product->price);
        $this->assertIsString($product->price);
    }

    public function test_product_images_are_cast_to_array()
    {
        $images = ['image1.jpg', 'image2.jpg'];
        $product = Product::factory()->create(['images' => $images]);

        $this->assertIsArray($product->images);
        $this->assertEquals($images, $product->images);
    }
}