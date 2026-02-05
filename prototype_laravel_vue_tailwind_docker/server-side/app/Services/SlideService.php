<?php

namespace App\Services;

use App\Models\Slide;
use Illuminate\Database\Eloquent\Collection;

/** Сервис CRUD для слайдов. */
class SlideService
{
    /** Список слайдов. */
    public function getAll(): Collection
    {
        return Slide::orderBy('id')->get();
    }

    /** Один слайд по id. */
    public function getById(int $id): ?Slide
    {
        return Slide::find($id);
    }

    /** Создание слайда. */
    public function create(array $data): Slide
    {
        return Slide::create($data);
    }

    /** Обновление слайда. */
    public function update(Slide $slide, array $data): Slide
    {
        $slide->update($data);
        return $slide->fresh();
    }

    /** Удаление слайда. */
    public function delete(Slide $slide): bool
    {
        return $slide->delete();
    }
}
