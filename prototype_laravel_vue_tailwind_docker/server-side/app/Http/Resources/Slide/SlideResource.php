<?php

namespace App\Http\Resources\Slide;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** API-ресурс слайда. */
class SlideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'badge' => $this->badge,
            'title' => $this->title,
            'description' => $this->description,
            'bg_color' => $this->bg_color,
            'image' => $this->image,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'old_price' => $this->old_price ? (float) $this->old_price : null,
            'discount' => $this->discount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
