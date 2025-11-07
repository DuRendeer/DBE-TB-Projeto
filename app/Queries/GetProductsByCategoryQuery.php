<?php

namespace App\Queries;

/**
 * CQRS Query - Represents a read operation (Get Products By Category)
 */
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

    /**
     * Get query filters as array
     */
    public function getFilters(): array
    {
        $filters = ['category_id' => $this->categoryId];

        if ($this->activeOnly) {
            $filters['active'] = true;
        }

        return $filters;
    }

    /**
     * Should filter by minimum stock?
     */
    public function hasMinStockFilter(): bool
    {
        return $this->minStock !== null;
    }

    /**
     * Get order by configuration
     */
    public function getOrderBy(): array
    {
        return [
            'column' => $this->orderBy ?? 'name',
            'direction' => $this->orderDirection
        ];
    }
}
