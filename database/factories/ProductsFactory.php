<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'cat_id' => $this->faker->numberBetween(1,10),
            'code' => $this->faker->sentence,
            'active' => $this->faker->numberBetween(0,1),
            'price' => $this->faker->numberBetween(1000, 500000),
            'type' => $this->faker->numberBetween(0,1),
        ];
    }
}
