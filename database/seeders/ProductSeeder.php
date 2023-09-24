<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name' => 'Pensil',
                'min_price' => 1000,
                'max_price' => 2000,
            ],
            [
                'name' => 'Kertas',
                'min_price' => 2000,
                'max_price' => 3000,
            ],
            [
                'name' => 'Rautan',
                'min_price' => 500,
                'max_price' => 1000,
            ],
            [
                'name' => 'Penghapus',
                'min_price' => 200,
                'max_price' => 300,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
