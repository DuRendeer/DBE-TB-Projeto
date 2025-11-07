<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Strategies\RegularPricingStrategy;
use App\Strategies\CategoryDiscountStrategy;
use App\Strategies\BulkDiscountStrategy;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit Test - Strategy Pattern
 *
 * Testa as diferentes estratégias de precificação
 */
class PricingStrategyTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Cria produto de teste
        $this->product = new Product([
            'name' => 'Test Product',
            'price' => 100.00
        ]);
    }

    public function test_regular_pricing_strategy(): void
    {
        $strategy = new RegularPricingStrategy();

        $price = $strategy->calculatePrice($this->product, 2);

        $this->assertEquals(200.00, $price);
        $this->assertEquals('regular', $strategy->getStrategyName());
    }

    public function test_category_discount_strategy_with_10_percent(): void
    {
        $strategy = new CategoryDiscountStrategy(10);

        $price = $strategy->calculatePrice($this->product, 2);

        // 200 - 10% = 180
        $this->assertEquals(180.00, $price);
        $this->assertEquals('category_discount', $strategy->getStrategyName());
        $this->assertEquals(10, $strategy->getDiscountPercentage());
    }

    public function test_category_discount_strategy_with_20_percent(): void
    {
        $strategy = new CategoryDiscountStrategy(20);

        $price = $strategy->calculatePrice($this->product, 1);

        // 100 - 20% = 80
        $this->assertEquals(80.00, $price);
    }

    public function test_bulk_discount_applies_only_above_minimum(): void
    {
        $strategy = new BulkDiscountStrategy(5, 15);

        // Quantidade abaixo do mínimo - sem desconto
        $priceBelow = $strategy->calculatePrice($this->product, 4);
        $this->assertEquals(400.00, $priceBelow);

        // Quantidade igual ao mínimo - com desconto
        $priceEqual = $strategy->calculatePrice($this->product, 5);
        $this->assertEquals(425.00, $priceEqual); // 500 - 15% = 425

        // Quantidade acima do mínimo - com desconto
        $priceAbove = $strategy->calculatePrice($this->product, 10);
        $this->assertEquals(850.00, $priceAbove); // 1000 - 15% = 850
    }

    public function test_pricing_service_can_change_strategy(): void
    {
        $service = new PricingService();

        // Estratégia padrão (regular)
        $regularPrice = $service->calculate($this->product, 5);
        $this->assertEquals(500.00, $regularPrice);
        $this->assertEquals('regular', $service->getCurrentStrategy());

        // Muda para bulk discount
        $service->setStrategy(new BulkDiscountStrategy(5, 15));
        $bulkPrice = $service->calculate($this->product, 5);
        $this->assertEquals(425.00, $bulkPrice);
        $this->assertEquals('bulk_discount', $service->getCurrentStrategy());
    }

    public function test_pricing_service_accepts_strategy_in_constructor(): void
    {
        $strategy = new CategoryDiscountStrategy(25);
        $service = new PricingService($strategy);

        $price = $service->calculate($this->product, 2);

        // 200 - 25% = 150
        $this->assertEquals(150.00, $price);
        $this->assertEquals('category_discount', $service->getCurrentStrategy());
    }
}
