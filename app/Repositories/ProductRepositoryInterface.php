<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository Pattern - Product Repository Interface
 *
 * Define o contrato para operações de acesso a dados de produtos.
 *
 * Benefícios:
 * - Dependency Inversion Principle: depende de abstração, não implementação
 * - Facilita testes com mocks
 * - Desacopla a lógica de negócio do ORM
 */
interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findAll(): Collection;

    public function findByCategory(int $categoryId): Collection;

    public function findActive(): Collection;

    public function create(array $data): Product;

    public function update(int $id, array $data): Product;

    public function delete(int $id): bool;

    public function search(string $term): Collection;

    public function findBySku(string $sku): ?Product;
}
