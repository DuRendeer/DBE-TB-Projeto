<?php

namespace App\Strategies;

use App\Models\Product;

class BulkDiscountStrategy implements PricingStrategyInterface
{
    private int $minQuantity;
    private float $discountPercentage;

    /**
     * @param int $minQuantity Quantidade mínima para desconto
     * @param float $discountPercentage Desconto em porcentagem (ex: 15 para 15%)
     */
    public function __construct(int $minQuantity = 5, float $discountPercentage = 15)
    {
        $this->minQuantity = $minQuantity;
        $this->discountPercentage = $discountPercentage;
    }

    public function calculatePrice(Product $product, int $quantity = 1): float
    {
        $basePrice = (float) $product->price * $quantity;

        // Aplica desconto apenas se atingir quantidade mínima
        if ($quantity >= $this->minQuantity) {
            $discount = $basePrice * ($this->discountPercentage / 100);
            return $basePrice - $discount;
        }

        return $basePrice;
    }

    public function getStrategyName(): string
    {
        return 'bulk_discount';
    }

    public function getMinQuantity(): int
    {
        return $this->minQuantity;
    }

    public function getDiscountPercentage(): float
    {
        return $this->discountPercentage;
    }
}
