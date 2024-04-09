<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessMarketFacebookFactory extends Factory
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
            'link' => $this->faker->url(),
            'market_id' => $this->faker->numberBetween(0,10),
        ];
    }
}
