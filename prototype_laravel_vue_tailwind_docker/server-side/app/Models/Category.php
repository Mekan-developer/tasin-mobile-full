<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Модель категории товаров (name, order, show_price, image_icon, views, is_active).
 */
class Category extends Model
{
    protected $fillable = ['name', 'order', 'show_price', 'image_icon', 'views', 'is_active'];

    protected $casts = [
        'show_price' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
        'views' => 'integer',
    ];
}
