<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/** Сервис CRUD для товаров. */
class ProductService
{
    /** Список товаров (опционально по category_id). */
    public function getAll(?int $categoryId = null): Collection
    {
        $query = Product::with('category')->orderBy('id');
        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }
        return $query->get();
    }

    /** Один товар по id. */
    public function getById(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    /** Создание товара. */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /** Обновление товара. */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh(['category']);
    }

    /** Удаление товара. */
    public function delete(Product $product): bool
    {
        return $product->delete();
    }
}
