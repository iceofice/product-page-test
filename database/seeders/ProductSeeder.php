<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDiscount;
use App\Models\ProductImage;
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
        // A product with 3 images and a discount
        Product::factory()
            ->count(1)
            ->has(ProductImage::factory()->count(3), 'images')
            ->has(ProductDiscount::factory(), 'discount')
            ->create();

        // A product with 1 image and no discount
        Product::factory()
            ->count(1)
            ->has(ProductImage::factory()->count(1), 'images')
            ->create();

        // A product with no images and a discount
        Product::factory()
            ->count(1)
            ->has(ProductDiscount::factory(), 'discount')
            ->create();

        // A product with no images and no discount
        Product::factory()
            ->count(1)
            ->create();
    }
}
