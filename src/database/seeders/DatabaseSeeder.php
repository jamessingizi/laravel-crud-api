<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'email' => 'admin@gmail.com', 'role' => 'admin'
        ]);
        User::factory(50)->create(['role' => 'user']);
    }
}
