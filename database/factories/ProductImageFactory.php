<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $imageIndex = 1;
        return [
            'path' => 'image-path-' . $imageIndex++ . '.png',
        ];
    }
}
