<?php

namespace App\Context;

use App\Models\Product;
use App\Strategies\PricingStrategyInterface;
use App\Strategies\RegularPricingStrategy;

class PricingContext
{
    private PricingStrategyInterface $strategy;

    public function __construct(?PricingStrategyInterface $strategy = null)
    {
        $this->strategy = $strategy ?? new RegularPricingStrategy();
    }

    public function setStrategy(PricingStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function calculatePrice(Product $product, int $quantity = 1): float
    {
        return $this->strategy->calculatePrice($product, $quantity);
    }

    public function getStrategyName(): string
    {
        return $this->strategy->getStrategyName();
    }
}
