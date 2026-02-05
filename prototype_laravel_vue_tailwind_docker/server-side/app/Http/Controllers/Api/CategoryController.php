<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/** API CRUD категорий. */
class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $service
    ) {}

    /** Список категорий. */
    public function index(): JsonResponse
    {
        $items = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($items),
        ]);
    }

    /** Одна категория. */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category),
        ]);
    }

    /** Создание категории (при загрузке файла сохраняем в storage и передаём имя). */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $this->prepareCategoryData($request);
        $category = $this->service->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Category created',
            'data' => new CategoryResource($category),
        ], 201);
    }

    /** Обновление категории (при загрузке файла сохраняем в storage и передаём имя). */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $this->prepareCategoryData($request, $category);
        $category = $this->service->update($category, $data);
        return response()->json([
            'success' => true,
            'message' => 'Category updated',
            'data' => new CategoryResource($category),
        ]);
    }

    /** Удаление категории. */
    public function destroy(Category $category): JsonResponse
    {
        $this->service->delete($category);
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }

    /** Подготавливает данные для create/update: при файле — сохраняет и подставляет имя. */
    private function prepareCategoryData(StoreCategoryRequest|UpdateCategoryRequest $request, ?Category $category = null): array
    {
        $data = $request->validated();
        if ($request->hasFile('image_icon')) {
            if ($category?->image_icon) {
                Storage::disk('public')->delete('categories/' . $category->image_icon);
            }
            $path = $request->file('image_icon')->store('categories', 'public');
            $data['image_icon'] = basename($path);
        }
        return $data;
    }
}
