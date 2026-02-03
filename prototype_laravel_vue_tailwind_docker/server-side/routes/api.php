<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Публичные маршруты (без аутентификации)
Route::get('status', [AuthController::class, 'status']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// Маршрут для обновления токена (проверяет refresh token)
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('check.refresh.token');

// Защищенные маршруты (требуют JWT токен)
Route::middleware(['auth:api', 'restaurant.active'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('profile', [AuthController::class, 'profile']);


    // Управление ролями и разрешениями
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/permissions', [RoleController::class, 'permissions']);
        Route::post('/{user}/assign-role', [RoleController::class, 'assignRole']);
        Route::post('/{user}/revoke-role', [RoleController::class, 'revokeRole']);
        Route::post('/{user}/sync-roles', [RoleController::class, 'syncRoles']);
        Route::post('/{user}/give-permission', [RoleController::class, 'givePermission']);
    });

     // Меню (пример с проверкой разрешений)
     Route::prefix('menu')->group(function () {
        Route::get('/', [MenuController::class, 'index'])
            ->middleware('permission:view_menu');

        Route::post('/', [MenuController::class, 'store'])
            ->middleware('permission:create_menu');

        Route::put('/{id}', [MenuController::class, 'update'])
            ->middleware('permission:edit_menu');

        Route::delete('/{id}', [MenuController::class, 'destroy'])
            ->middleware('permission:delete_menu');
    });

    // Управление ресторанами
    Route::prefix('restaurants')->group(function () {
        // Создать ресторан (только super-admin)
        Route::post('/', [RestaurantController::class, 'store'])
            ->middleware('role:super-admin');

        // Получить все рестораны (только super-admin)
        Route::get('/', [RestaurantController::class, 'index'])
            ->middleware('role:super-admin');

        // Получить мой ресторан (для owner)
        Route::get('/my', [RestaurantController::class, 'myRestaurant'])
            ->middleware('role:owner');

        // Получить ресторан по ID
        Route::get('/{restaurant}', [RestaurantController::class, 'show']);

        // Обновить ресторан
        Route::put('/{restaurant}', [RestaurantController::class, 'update']);

        // Удалить ресторан (только super-admin)
        Route::delete('/{restaurant}', [RestaurantController::class, 'destroy'])
            ->middleware('role:super-admin');

        // Получить работников ресторана
        Route::get('/{restaurant}/employees', [RestaurantController::class, 'employees']);
    });

    // Управление пользователями (работниками)
    Route::prefix('users')
        ->middleware('role:owner')
        ->group(function () {
            // Получить всех сотрудников своего ресторана
            Route::get('/', [UserController::class, 'index']);

            // Создать работника (только owner)
            Route::post('/', [UserController::class, 'store']);

            // Получить пользователя по ID
            Route::get('/{user}', [UserController::class, 'show']);

            // Обновить пользователя
            Route::put('/{user}', [UserController::class, 'update']);

            // Удалить пользователя
            Route::delete('/{user}', [UserController::class, 'destroy']);
        });

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
