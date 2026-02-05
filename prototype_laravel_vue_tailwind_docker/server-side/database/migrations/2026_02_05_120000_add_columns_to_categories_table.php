<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Добавляет колонки в таблицу categories по структуре из db.json.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name');
            $table->unsignedInteger('order')->default(1);
            $table->boolean('show_price')->default(true);
            $table->string('image_icon')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'order', 'show_price', 'image_icon']);
        });
    }
};
