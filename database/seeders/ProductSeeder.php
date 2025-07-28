<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Nasi Goreng',
                'description' => 'Nasi goreng khas Indonesia.',
                'price' => 25000,
                'category' => 'Makanan',
                'image' => 'products/nasi-goreng.jpg',
            ],
            [
                'name' => 'Es Teh Manis',
                'description' => 'Minuman dingin teh manis.',
                'price' => 8000,
                'category' => 'Minuman',
                'image' => 'products/es-teh-manis.jpg',
            ],
            [
                'name' => 'Mie Ayam',
                'description' => 'Mie ayam dengan topping melimpah.',
                'price' => 20000,
                'category' => 'Makanan',
                'image' => 'products/mie-ayam.jpg',
            ],
            [
                'name' => 'Jus Alpukat',
                'description' => 'Jus alpukat segar.',
                'price' => 15000,
                'category' => 'Minuman',
                'image' => 'products/jus-alpukat.jpg',
            ],
            [
                'name' => 'Sate Ayam',
                'description' => 'Sate ayam dengan bumbu kacang khas.',
                'price' => 30000,
                'category' => 'Makanan',
                'image' => 'products/sate-ayam.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
