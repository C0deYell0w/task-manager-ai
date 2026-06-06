<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => \App\Enums\RoleEnum::Admin->value,
        ]);

        \App\Models\User::factory(3)->create([
            'role' => \App\Enums\RoleEnum::User->value,
        ]);

        \App\Models\Task::factory(20)->create();
    }
}
