<?php

namespace App\Services;

use App\Models\Product;
use App\Strategies\PricingStrategyInterface;
use App\Strategies\RegularPricingStrategy;

/**
 * Strategy Pattern Implementation
 *
 * Service responsável por calcular preços usando diferentes estratégias.
 *
 * Benefícios:
 * - Permite trocar algoritmos de cálculo em tempo de execução
 * - Facilita adicionar novas estratégias sem modificar código existente (Open/Closed)
 * - Separa a lógica de cálculo de preços do resto da aplicação (Single Responsibility)
 * - Usa Dependency Injection para receber a estratégia
 */
class PricingService
{
    private PricingStrategyInterface $strategy;

    /**
     * Dependency Injection da estratégia de precificação
     */
    public function __construct(?PricingStrategyInterface $strategy = null)
    {
        $this->strategy = $strategy ?? new RegularPricingStrategy();
    }

    /**
     * Set a pricing strategy at runtime
     */
    public function setStrategy(PricingStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * Calculate price using the current strategy
     */
    public function calculate(Product $product, int $quantity = 1): float
    {
        return $this->strategy->calculatePrice($product, $quantity);
    }

    /**
     * Get the current strategy name
     */
    public function getCurrentStrategy(): string
    {
        return $this->strategy->getStrategyName();
    }
}
