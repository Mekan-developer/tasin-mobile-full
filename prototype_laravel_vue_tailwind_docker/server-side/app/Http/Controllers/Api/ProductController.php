<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** API CRUD товаров. */
class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    /** Список товаров (опционально ?category_id=). */
    public function index(Request $request): JsonResponse
    {
        $categoryId = $request->query('category_id') ? (int) $request->query('category_id') : null;
        $items = $this->service->getAll($categoryId);
        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($items),
        ]);
    }

    /** Один товар. */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }

    /** Создание товара. */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->validated());
        $product->load('category');
        return response()->json([
            'success' => true,
            'message' => 'Product created',
            'data' => new ProductResource($product),
        ], 201);
    }

    /** Обновление товара. */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->service->update($product, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Product updated',
            'data' => new ProductResource($product),
        ]);
    }

    /** Удаление товара. */
    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product);
        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }
}
