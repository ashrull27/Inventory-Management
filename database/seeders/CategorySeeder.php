<?php

namespace Database\Seeders;

use App\Models\Category;
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
                'name' => 'Electronics',
                'description' => 'Electronic devices and gadgets',
            ],
            [
                'name' => 'Accessories',
                'description' => 'Computer and electronic accessories',
            ],
            [
                'name' => 'Supplies',
                'description' => 'Office and general supplies',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}