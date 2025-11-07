<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepositoryInterface;
use App\Services\PricingService;
use App\Strategies\BulkDiscountStrategy;
use App\Strategies\RegularPricingStrategy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Refactored Product Controller
 *
 * Aplicação dos Princípios SOLID:
 *
 * 1. Single Responsibility: Controller apenas coordena requests/responses,
 *    lógica de negócio está em Services e Repositories
 *
 * 2. Dependency Inversion: Depende de abstrações (interfaces),
 *    não de implementações concretas
 *
 * 3. Open/Closed: Pode estender funcionalidades (ex: novos repositories)
 *    sem modificar o código existente
 */
class ProductControllerRefactored extends Controller
{
    /**
     * Dependency Injection via Constructor
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly PricingService $pricingService
    ) {
    }

    /**
     * List all active products
     */
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findActive();
        return response()->json($products);
    }

    /**
     * Store a new product
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|unique:products,sku',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $product = $this->productRepository->create($validated);

        return response()->json($product, 201);
    }

    /**
     * Show a product
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    /**
     * Update a product
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:0',
            'sku' => 'sometimes|string|unique:products,sku,' . $id,
            'images' => 'nullable|array',
            'images.*' => 'string',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $product = $this->productRepository->update($id, $validated);

        return response()->json($product);
    }

    /**
     * Delete a product
     */
    public function destroy(int $id): JsonResponse
    {
        $this->productRepository->delete($id);
        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Calculate price with different strategies (Strategy Pattern)
     */
    public function calculatePrice(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'strategy' => 'sometimes|string|in:regular,bulk'
        ]);

        $product = $this->productRepository->findById($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Seleciona estratégia de precificação
        $strategy = match ($validated['strategy'] ?? 'regular') {
            'bulk' => new BulkDiscountStrategy(5, 15),
            default => new RegularPricingStrategy()
        };

        $this->pricingService->setStrategy($strategy);
        $finalPrice = $this->pricingService->calculate($product, $validated['quantity']);

        return response()->json([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'base_price' => $product->price,
            'quantity' => $validated['quantity'],
            'strategy' => $this->pricingService->getCurrentStrategy(),
            'final_price' => $finalPrice
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'term' => 'required|string|min:2'
        ]);

        $products = $this->productRepository->search($validated['term']);

        return response()->json($products);
    }
}
