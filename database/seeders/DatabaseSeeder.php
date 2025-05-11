<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'user_id' => '@Admin',
                'role' => 'admin',
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'User',
                'email' => 'user@gmail.com',
                'user_id' => '@user',
                'role' => 'user',
                'password' => Hash::make('123456'),
            ]
        ];
        foreach($users as $user){
            User::factory()->create($user);
        }
    }
}
