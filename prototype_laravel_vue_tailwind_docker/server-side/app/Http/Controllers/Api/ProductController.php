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
        $data = $this->prepareProductData($request);
        $product = $this->service->create($data);
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
        $data = $this->prepareProductData($request, $product);
        $product = $this->service->update($product, $data);
        return response()->json([
            'success' => true,
            'message' => 'Product updated',
            'data' => new ProductResource($product),
        ]);
    }

    /** Подготавливает данные: при FormData — сохраняет файлы в storage/products, объединяет с existing_images. */
    private function prepareProductData(StoreProductRequest|UpdateProductRequest $request, ?Product $product = null): array
    {
        $data = $request->validated();
        if (! $request->hasFile('images') && ! $request->has('existing_images')) {
            return $data;
        }
        $existing = json_decode($request->input('existing_images', '[]'), true);
        $existing = is_array($existing) ? $existing : [];
        $newFiles = $request->file('images');
        $newFiles = is_array($newFiles) ? $newFiles : ($newFiles ? [$newFiles] : []);
        $newFilenames = [];
        foreach ($newFiles as $file) {
            if ($file && $file->isValid()) {
                $path = $file->store('products', 'public');
                $newFilenames[] = basename($path);
            }
        }
        $data['images'] = array_merge($existing, $newFilenames);
        return $data;
    }

    /** Удаление товара. */
    public function destroy(Product $product): JsonResponse
    {
        $this->service->delete($product);
        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }
}
