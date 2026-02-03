<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создание разрешений
        $permissions = [
            // Управление меню
            'view_menu',
            'create_menu',
            'edit_menu',
            'delete_menu',

            // Управление категориями
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',

            // Управление заказами
            'view_orders',
            'edit_orders',
            'delete_orders',

            // Управление пользователями
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Настройки ресторана
            'view_settings',
            'edit_settings',

            // Аналитика
            'view_analytics',

            // Управление столиками
            'view_tables',
            'create_tables',
            'edit_tables',
            'delete_tables',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Создание ролей
        $superAdminRole = Role::updateOrCreate(['name' => 'super-admin', 'guard_name' => 'api']);
        $ownerRole      = Role::updateOrCreate(['name' => 'owner', 'guard_name' => 'api']);
        $adminRole      = Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $managerRole    = Role::updateOrCreate(['name' => 'manager', 'guard_name' => 'api']);
        $waiterRole     = Role::updateOrCreate(['name' => 'waiter', 'guard_name' => 'api']);


        // Назначение разрешений ролям
        $superAdminRole->givePermissionTo(Permission::all());

        $adminPermissions = [
            'view_menu', 'create_menu', 'edit_menu', 'delete_menu',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_orders', 'edit_orders',
            'view_users', 'create_users', 'edit_users',
            'view_settings', 'edit_settings',
            'view_analytics',
            'view_tables', 'create_tables', 'edit_tables', 'delete_tables'
        ];
        // Owner имеет тот же набор, чтобы управлять своим рестораном
        $ownerPermissions = $adminPermissions;

        $adminRole->givePermissionTo($adminPermissions);
        $ownerRole->givePermissionTo($ownerPermissions);

        $managerPermissions = [
            'view_menu', 'edit_menu',
            'view_categories', 'edit_categories',
            'view_orders', 'edit_orders',
            'view_analytics',
            'view_tables', 'edit_tables'
        ];
        $managerRole->givePermissionTo($managerPermissions);

        $waiterPermissions = [
            'view_menu',
            'view_categories',
            'view_orders', 'edit_orders',
            'view_tables'
        ];
        $waiterRole->givePermissionTo($waiterPermissions);


    }
}
