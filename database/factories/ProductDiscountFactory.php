<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductDiscountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $types = ['percent', 'amount'];
        return [
            'type' => $this->faker->randomElement($types),
            'discount' => $this->faker->numberBetween(5, 50),
        ];
    }
}
