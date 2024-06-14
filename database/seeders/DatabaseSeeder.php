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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'superadmin@example.com',
            'role' => 'ADMIN',
        ]);
        User::factory()->create([
            'name' => 'LAIBRAIAN',
            'email' => 'syed10@example.com',
            'role' => 'LAIBRAIAN',
        ]);
        User::factory()->create([
            'name' => 'SYed Numan',
            'email' => 'syed11@example.com',
            'role' => 'STUDENT',
        ]);
        User::factory()->create([
            'name' => 'SYed Jamil',
            'email' => 'syed110@example.com',
            'role' => 'STUDENT',
        ]);
    }
}
