<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Banho e Tosa Completa',
                'description' => 'Banho completo com shampoo premium, tosa higiênica e corte de unhas',
                'price' => 60.00,
                'duration_minutes' => 120,
                'active' => true
            ],
            [
                'name' => 'Banho Simples',
                'description' => 'Banho com shampoo e secagem',
                'price' => 35.00,
                'duration_minutes' => 60,
                'active' => true
            ],
            [
                'name' => 'Tosa Higiênica',
                'description' => 'Tosa das partes íntimas, patas e focinho',
                'price' => 25.00,
                'duration_minutes' => 30,
                'active' => true
            ],
            [
                'name' => 'Corte de Unhas',
                'description' => 'Corte e lixamento das unhas',
                'price' => 15.00,
                'duration_minutes' => 20,
                'active' => true
            ],
            [
                'name' => 'Limpeza de Ouvidos',
                'description' => 'Limpeza e higienização dos ouvidos',
                'price' => 20.00,
                'duration_minutes' => 15,
                'active' => true
            ]
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
