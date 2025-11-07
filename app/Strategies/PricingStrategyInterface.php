<?php

namespace App\Strategies;

use App\Models\Product;

interface PricingStrategyInterface
{
    /**
     * Calculate the final price for a product
     *
     * @param Product $product
     * @param int $quantity
     * @return float
     */
    public function calculatePrice(Product $product, int $quantity = 1): float;

    /**
     * Get the strategy name
     *
     * @return string
     */
    public function getStrategyName(): string;
}
