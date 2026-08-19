<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed admin user for testing
        User::create([
            'name' => 'Admin Tester',
            'username' => 'admintest',
            'email' => 'admin@example.com',
            'password_hash' => bcrypt('password'),
            'user_type' => 'admin',
        ]);

        // Seed test user
        User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password_hash' => bcrypt('password'),
            'user_type' => 'user',
        ]);
    }
}
