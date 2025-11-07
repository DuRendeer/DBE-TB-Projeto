<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository Pattern - Product Repository Implementation
 *
 * Implementa o acesso a dados de produtos usando Eloquent.
 * Centraliza todas as queries relacionadas a produtos.
 */
class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    public function findAll(): Collection
    {
        return Product::with('category')->get();
    }

    public function findByCategory(int $categoryId): Collection
    {
        return Product::with('category')
            ->where('category_id', $categoryId)
            ->get();
    }

    public function findActive(): Collection
    {
        return Product::with('category')
            ->where('active', true)
            ->get();
    }

    public function create(array $data): Product
    {
        $product = Product::create($data);
        return $this->findById($product->id);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }

    public function search(string $term): Collection
    {
        return Product::with('category')
            ->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%")
            ->get();
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::with('category')
            ->where('sku', $sku)
            ->first();
    }
}
