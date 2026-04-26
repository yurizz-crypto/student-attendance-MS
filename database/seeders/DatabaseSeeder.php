<?php

namespace Database\Seeders;

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
        // 1. Create an Admin Account
        User::factory()->create([
            'first_name' => 'System',
            'last_name' => 'Admin',
            'identity_id' => 'ADMIN-001',
            'email' => 'admin@cmu.edu.ph',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        // 2. Create a Faculty Account
        User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'identity_id' => 'FAC-001',
            'email' => 'faculty@cmu.edu.ph',
            'role' => 'faculty',
            'password' => Hash::make('password123'),
        ]);

        // 3. Create a Student Account
        User::factory()->create([
            'first_name' => 'Yuri',
            'last_name' => 'Salise',
            'identity_id' => 'STUD-001',
            'email' => 'student@cmu.edu.ph',
            'role' => 'student',
            'password' => Hash::make('password123'),
        ]);
    }
}