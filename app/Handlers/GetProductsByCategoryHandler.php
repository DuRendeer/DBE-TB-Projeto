<?php

namespace App\Handlers;

use App\Queries\GetProductsByCategoryQuery;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * CQRS Query Handler - Get Products By Category
 */
class GetProductsByCategoryHandler
{
    /**
     * Handle the query
     */
    public function handle(GetProductsByCategoryQuery $query): Collection
    {
        $builder = Product::with('category')
            ->where($query->getFilters());

        // Aplica filtro de estoque mínimo se especificado
        if ($query->hasMinStockFilter()) {
            $builder->where('stock_quantity', '>=', $query->minStock);
        }

        // Aplica ordenação
        $orderBy = $query->getOrderBy();
        $builder->orderBy($orderBy['column'], $orderBy['direction']);

        return $builder->get();
    }
}
