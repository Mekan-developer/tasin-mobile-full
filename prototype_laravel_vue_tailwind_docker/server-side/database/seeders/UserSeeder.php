<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@restaurant.com',
                'password' => bcrypt('password123'),
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@restaurant.com',
                'password' => bcrypt('password123'),
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@restaurant.com',
                'password' => bcrypt('password123'),
            ],
            [
                'name' => 'Waiter',
                'email' => 'waiter@restaurant.com',
                'password' => bcrypt('password123'),
            ],
        ];
        // foreach ($users as $user) {
        //     User::updateOrCreate(['email' => $user['email']], $user);
        // }
        // $superAdmin = User::where('email', 'superadmin@restaurant.com')->first();
        // if ($superAdmin) {
        //     $superAdmin->assignRole('super-admin');
        // }
        // $admin = User::where('email', 'admin@restaurant.com')->first();
        // if ($admin) {
        //     $admin->assignRole('admin');
        // }
        // $manager = User::where('email', 'manager@restaurant.com')->first();
        // if ($manager) {
        //     $manager->assignRole('manager');
        // }
        // $waiter = User::where('email', 'waiter@restaurant.com')->first();
        // if ($waiter) {
        //     $waiter->assignRole('waiter');
        // }
    }
}
