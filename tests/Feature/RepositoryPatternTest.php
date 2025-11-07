<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Repositories\ProductRepository;

/**
 * Feature Test - Repository Pattern
 *
 * Testa a implementação do padrão Repository
 */
class RepositoryPatternTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $repository;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new ProductRepository();

        $this->category = Category::create([
            'name' => 'Ração',
            'description' => 'Alimentos para pets'
        ]);
    }

    public function test_can_create_product_through_repository(): void
    {
        $data = [
            'category_id' => $this->category->id,
            'name' => 'Ração Premium',
            'description' => 'Ração de alta qualidade',
            'price' => 150.00,
            'stock_quantity' => 50,
            'sku' => 'RAC-PREM-001',
            'active' => true
        ];

        $product = $this->repository->create($data);

        $this->assertNotNull($product);
        $this->assertEquals('Ração Premium', $product->name);
        $this->assertNotNull($product->category);

        $this->assertDatabaseHas('products', [
            'name' => 'Ração Premium',
            'sku' => 'RAC-PREM-001'
        ]);
    }

    public function test_can_find_product_by_id(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Coleira',
            'description' => 'Coleira resistente',
            'price' => 35.00,
            'stock_quantity' => 100,
            'sku' => 'COL-001',
            'active' => true
        ]);

        $found = $this->repository->findById($product->id);

        $this->assertNotNull($found);
        $this->assertEquals('Coleira', $found->name);
        $this->assertNotNull($found->category);
    }

    public function test_can_find_products_by_category(): void
    {
        // Cria produtos na categoria
        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Ração 1',
            'description' => 'Desc 1',
            'price' => 100.00,
            'stock_quantity' => 10,
            'sku' => 'RAC-001',
            'active' => true
        ]);

        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Ração 2',
            'description' => 'Desc 2',
            'price' => 120.00,
            'stock_quantity' => 15,
            'sku' => 'RAC-002',
            'active' => true
        ]);

        // Cria produto em outra categoria
        $otherCategory = Category::create([
            'name' => 'Brinquedos',
            'description' => 'Brinquedos diversos'
        ]);

        Product::create([
            'category_id' => $otherCategory->id,
            'name' => 'Bolinha',
            'description' => 'Bolinha de borracha',
            'price' => 15.00,
            'stock_quantity' => 50,
            'sku' => 'BRI-001',
            'active' => true
        ]);

        $products = $this->repository->findByCategory($this->category->id);

        $this->assertCount(2, $products);
        $this->assertTrue($products->every(fn($p) => $p->category_id === $this->category->id));
    }

    public function test_can_find_only_active_products(): void
    {
        // Produto ativo
        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Ativo',
            'description' => 'Produto ativo',
            'price' => 50.00,
            'stock_quantity' => 10,
            'sku' => 'ACT-001',
            'active' => true
        ]);

        // Produto inativo
        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Inativo',
            'description' => 'Produto inativo',
            'price' => 50.00,
            'stock_quantity' => 10,
            'sku' => 'INA-001',
            'active' => false
        ]);

        $activeProducts = $this->repository->findActive();

        $this->assertCount(1, $activeProducts);
        $this->assertEquals('Ativo', $activeProducts->first()->name);
        $this->assertTrue($activeProducts->first()->active);
    }

    public function test_can_update_product_through_repository(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Nome Original',
            'description' => 'Descrição original',
            'price' => 100.00,
            'stock_quantity' => 10,
            'sku' => 'TEST-001',
            'active' => true
        ]);

        $updated = $this->repository->update($product->id, [
            'name' => 'Nome Atualizado',
            'price' => 120.00
        ]);

        $this->assertEquals('Nome Atualizado', $updated->name);
        $this->assertEquals(120.00, $updated->price);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Nome Atualizado',
            'price' => 120.00
        ]);
    }

    public function test_can_delete_product_through_repository(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Para Deletar',
            'description' => 'Será deletado',
            'price' => 50.00,
            'stock_quantity' => 5,
            'sku' => 'DEL-001',
            'active' => true
        ]);

        $result = $this->repository->delete($product->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    public function test_can_search_products(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Ração Premium Gold',
            'description' => 'Alta qualidade',
            'price' => 150.00,
            'stock_quantity' => 10,
            'sku' => 'RAC-001',
            'active' => true
        ]);

        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Coleira Premium',
            'description' => 'Resistente',
            'price' => 50.00,
            'stock_quantity' => 20,
            'sku' => 'COL-001',
            'active' => true
        ]);

        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Brinquedo Simples',
            'description' => 'Brinquedo básico',
            'price' => 25.00,
            'stock_quantity' => 30,
            'sku' => 'BRI-001',
            'active' => true
        ]);

        $results = $this->repository->search('Premium');

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains('name', 'Ração Premium Gold'));
        $this->assertTrue($results->contains('name', 'Coleira Premium'));
    }

    public function test_can_find_product_by_sku(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'name' => 'Produto Único',
            'description' => 'SKU único',
            'price' => 75.00,
            'stock_quantity' => 5,
            'sku' => 'UNIQUE-SKU-123',
            'active' => true
        ]);

        $product = $this->repository->findBySku('UNIQUE-SKU-123');

        $this->assertNotNull($product);
        $this->assertEquals('Produto Único', $product->name);
        $this->assertEquals('UNIQUE-SKU-123', $product->sku);
    }
}
