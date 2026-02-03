<?php
// database/seeders/RestaurantSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\User;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {


        $restaurants = [
            [
                'name' => 'Итальянская пиццерия "Mamma Mia"',
                'description' => 'Аутентичная итальянская кухня, свежая паста и пицца из дровяной печи',
                'address' => 'ул. Пушкина, 15, Москва',
                'phone' => '+7 (495) 123-45-67',
                'owner_id' => 1,
            ],
            [
                'name' => 'Ресторан "Суши-Мастер"',
                'description' => 'Японская кухня, свежие морепродукты и широкий выбор суши',
                'address' => 'ул. Ленина, 42, Москва',
                'phone' => '+7 (495) 234-56-78',
                'owner_id' => User::all()->first()->id,
            ],
            [
                'name' => 'Кафе "Бургер Хаус"',
                'description' => 'Лучшие бургеры в городе, картофель фри и милкшейки',
                'address' => 'пр. Мира, 89, Москва',
                'phone' => '+7 (495) 345-67-89',
                'owner_id' => User::all()->first()->id,
            ],
            [
                'name' => 'Вегетарианское кафе "Зелень"',
                'description' => 'Здоровая вегетарианская и веганская еда из органических продуктов',
                'address' => 'ул. Чехова, 33, Москва',
                'phone' => '+7 (495) 456-78-90',
                'owner_id' => User::all()->first()->id,
            ],
            [
                'name' => 'Кофейня "Аромат"',
                'description' => 'Кофе собственной обжарки, десерты и завтраки',
                'address' => 'ул. Горького, 12, Москва',
                'phone' => '+7 (495) 567-89-01',
                'owner_id' => User::all()->first()->id,
            ],
        ];

        foreach ($restaurants as $restaurant) {
            Restaurant::create($restaurant);
        }
    }
}
