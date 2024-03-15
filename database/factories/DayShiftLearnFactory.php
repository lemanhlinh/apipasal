<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DayShiftLearnFactory extends Factory
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
            'active' => $this->faker->numberBetween(0,1),
        ];
    }
}
