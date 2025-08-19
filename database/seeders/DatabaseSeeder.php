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
        $this->call([
            CategorySeeder::class,
            ServiceSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Eduardo Sochodolak',
            'email' => 'eduardo@petshop.com',
        ]);

        User::factory()->create([
            'name' => 'Johann Matheus',
            'email' => 'johann@petshop.com',
        ]);

        User::factory()->create([
            'name' => 'Alexsandro Lemos',
            'email' => 'alexsandro@petshop.com',
        ]);
    }
}
