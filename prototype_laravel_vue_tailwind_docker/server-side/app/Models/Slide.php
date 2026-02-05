<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель слайда баннера/карусели (badge, title, price, image и т.д.).
 */
class Slide extends Model
{
    protected $fillable = [
        'badge', 'title', 'description', 'bg_color', 'image',
        'price', 'currency', 'old_price', 'discount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
    ];
}
