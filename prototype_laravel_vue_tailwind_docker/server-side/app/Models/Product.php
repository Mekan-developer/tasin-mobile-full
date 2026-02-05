<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель товара: название, цена, изображения, варианты, связь с категорией.
 */
class Product extends Model
{
    protected $fillable = [
        'category_id', 'active_image', 'name', 'description',
        'price', 'currency', 'images', 'views', 'is_new', 'variants',
    ];

    /** variants: массив [{ "model": "iPhone 16 Pro Max", "price": "5656TMT" }, { "model": "...", "price": "" }, ...] */
    protected $casts = [
        'images' => 'array',
        'variants' => 'array',
        'is_new' => 'boolean',
        'price' => 'decimal:2',
        'views' => 'integer',
        'active_image' => 'integer',
    ];

    /** Связь с категорией. */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
