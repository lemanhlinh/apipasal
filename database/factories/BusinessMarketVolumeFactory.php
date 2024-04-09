<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessMarketVolumeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'year' => $this->faker->year(),
            'total_year' => $this->faker->numberBetween(1,5),
            'more_level' => '',
            'market_id' => $this->faker->numberBetween(0,10),
        ];
    }
}
