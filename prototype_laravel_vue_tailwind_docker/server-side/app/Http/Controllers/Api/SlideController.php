<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Slide\StoreSlideRequest;
use App\Http\Requests\Slide\UpdateSlideRequest;
use App\Http\Resources\Slide\SlideResource;
use App\Models\Slide;
use App\Services\SlideService;
use Illuminate\Http\JsonResponse;

/** API CRUD слайдов. */
class SlideController extends Controller
{
    public function __construct(
        protected SlideService $service
    ) {}

    /** Список слайдов. */
    public function index(): JsonResponse
    {
        $items = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => SlideResource::collection($items),
        ]);
    }

    /** Один слайд. */
    public function show(Slide $slide): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new SlideResource($slide),
        ]);
    }

    /** Создание слайда. */
    public function store(StoreSlideRequest $request): JsonResponse
    {
        $slide = $this->service->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Slide created',
            'data' => new SlideResource($slide),
        ], 201);
    }

    /** Обновление слайда. */
    public function update(UpdateSlideRequest $request, Slide $slide): JsonResponse
    {
        $slide = $this->service->update($slide, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Slide updated',
            'data' => new SlideResource($slide),
        ]);
    }

    /** Удаление слайда. */
    public function destroy(Slide $slide): JsonResponse
    {
        $this->service->delete($slide);
        return response()->json(['success' => true, 'message' => 'Slide deleted']);
    }
}
