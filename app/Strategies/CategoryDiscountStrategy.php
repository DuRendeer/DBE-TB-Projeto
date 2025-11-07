<?php

namespace App\Strategies;

use App\Models\Product;

class CategoryDiscountStrategy implements PricingStrategyInterface
{
    private float $discountPercentage;

    /**
     * @param float $discountPercentage Desconto em porcentagem (ex: 10 para 10%)
     */
    public function __construct(float $discountPercentage = 10)
    {
        $this->discountPercentage = $discountPercentage;
    }

    public function calculatePrice(Product $product, int $quantity = 1): float
    {
        $basePrice = (float) $product->price * $quantity;
        $discount = $basePrice * ($this->discountPercentage / 100);

        return $basePrice - $discount;
    }

    public function getStrategyName(): string
    {
        return 'category_discount';
    }

    public function getDiscountPercentage(): float
    {
        return $this->discountPercentage;
    }
}
