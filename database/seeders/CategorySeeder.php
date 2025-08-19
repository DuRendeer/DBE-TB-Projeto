<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ração e Alimentação',
                'description' => 'Rações, petiscos e suplementos para cães e gatos',
                'active' => true
            ],
            [
                'name' => 'Brinquedos',
                'description' => 'Brinquedos para entretenimento e exercício dos pets',
                'active' => true
            ],
            [
                'name' => 'Higiene e Cuidados',
                'description' => 'Produtos para higiene, shampoos e cuidados gerais',
                'active' => true
            ],
            [
                'name' => 'Acessórios',
                'description' => 'Coleiras, guias, camas e outros acessórios',
                'active' => true
            ],
            [
                'name' => 'Medicamentos',
                'description' => 'Medicamentos e produtos veterinários',
                'active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
