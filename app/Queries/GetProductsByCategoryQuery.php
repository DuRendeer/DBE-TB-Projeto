<?php

namespace App\Queries;

class GetProductsByCategoryQuery
{
    public function __construct(
        public readonly int $categoryId,
        public readonly bool $activeOnly = true,
        public readonly ?int $minStock = null,
        public readonly ?string $orderBy = 'name',
        public readonly string $orderDirection = 'asc'
    ) {
    }
}
