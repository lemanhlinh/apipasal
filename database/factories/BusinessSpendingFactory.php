<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessSpendingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name(),
            'month' => $this->faker->numberBetween(1,5),
            'active' => $this->faker->numberBetween(0,1),
        ];
    }
}
