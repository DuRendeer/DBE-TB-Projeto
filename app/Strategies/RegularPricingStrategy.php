<?php

namespace App\Strategies;

use App\Models\Product;

class RegularPricingStrategy implements PricingStrategyInterface
{
    public function calculatePrice(Product $product, int $quantity = 1): float
    {
        return (float) $product->price * $quantity;
    }

    public function getStrategyName(): string
    {
        return 'regular';
    }
}
