<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель категории товаров (name, order, show_price, image_icon).
 */
class Category extends Model
{
    protected $fillable = ['name', 'order', 'show_price', 'image_icon'];

    protected $casts = [
        'show_price' => 'boolean',
        'order' => 'integer',
    ];
}
