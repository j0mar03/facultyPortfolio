<?php

namespace Database\Seeders;

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
            ['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => \Hash::make('password'), 'role' => 'admin'],
            ['name' => 'Chair DICT', 'email' => 'chair.dict@example.com', 'password' => \Hash::make('password'), 'role' => 'chair'],
            ['name' => 'Faculty One', 'email' => 'faculty1@example.com', 'password' => \Hash::make('password'), 'role' => 'faculty'],
            ['name' => 'Auditor', 'email' => 'auditor@example.com', 'password' => \Hash::make('password'), 'role' => 'auditor'],
        ];

        foreach ($users as $userData) {
            \App\Models\User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
