<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::where('name', 'Electronics')->first();
        $accessories = Category::where('name', 'Accessories')->first();
        $supplies = Category::where('name', 'Supplies')->first();

        $products = [
            [
                'name' => 'Laptop',
                'category_id' => $electronics->id,
                'unit_price' => 3500.00,
                'stock_quantity' => 20,
            ],
            [
                'name' => 'Keyboard',
                'category_id' => $accessories->id,
                'unit_price' => 120.00,
                'stock_quantity' => 50,
            ],
            [
                'name' => 'Mouse',
                'category_id' => $accessories->id,
                'unit_price' => 60.00,
                'stock_quantity' => 100,
            ],
            [
                'name' => 'A4 Paper',
                'category_id' => $supplies->id,
                'unit_price' => 45.00,
                'stock_quantity' => 30,
            ],
            [
                'name' => 'LAN Cable',
                'category_id' => $supplies->id,
                'unit_price' => 3.00,
                'stock_quantity' => 500,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}