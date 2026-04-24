<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Panitia Seminar',
            'email' => 'panitia@example.com',
            'password' => Hash::make('password'),
            'role' => 'panitia',
        ]);

        User::create([
            'name' => 'Panitia Konser',
            'email' => 'panitia2@example.com',
            'password' => Hash::make('password'),
            'role' => 'panitia',
        ]);

        User::create([
            'name' => 'User Demo',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}