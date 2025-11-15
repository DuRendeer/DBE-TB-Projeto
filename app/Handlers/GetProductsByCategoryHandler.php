<?php

namespace App\Handlers;

use App\Queries\GetProductsByCategoryQuery;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class GetProductsByCategoryHandler
{
    public function handle(GetProductsByCategoryQuery $query): Collection
    {
        $builder = Product::with('category')
            ->where('category_id', $query->categoryId);

        if ($query->activeOnly) {
            $builder->where('active', true);
        }

        if ($query->minStock !== null) {
            $builder->where('stock_quantity', '>=', $query->minStock);
        }

        $builder->orderBy($query->orderBy ?? 'name', $query->orderDirection);

        return $builder->get();
    }
}
