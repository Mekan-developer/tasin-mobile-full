<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

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

    /** Удаление категории. */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
