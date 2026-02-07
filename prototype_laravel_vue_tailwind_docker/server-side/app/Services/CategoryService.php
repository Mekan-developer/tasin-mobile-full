<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

/** Сервис CRUD для категорий. */
class CategoryService
{
    /** Список категорий по order. */
    public function getAll(): Collection
    {
        return Category::orderBy('order')->get();
    }

    /** Одна категория по id. */
    public function getById(int $id): ?Category
    {
        return Category::find($id);
    }

    /** Создание категории. */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /** Обновление категории. */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    /** Удаление категории и связанного изображения. */
    public function delete(Category $category): bool
    {
        if ($category->image_icon) {
            Storage::disk('public')->delete('categories/' . $category->image_icon);
        }
        return $category->delete();
    }
}
