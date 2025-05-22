<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->words(3, true);
        return [
            'name' => ucfirst($name),
            'description' => $this->faker->paragraph,
            'slug' => Str::slug($name),
            'price' => $this->faker->numberBetween(100, 500),
            'active' => true,
        ];
    }
}
