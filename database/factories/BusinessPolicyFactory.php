<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessPolicyFactory extends Factory
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
            'type' => $this->faker->numberBetween(0,1),
            'type_promotion' => $this->faker->numberBetween(0,1),
            'promotion' => $this->faker->numberBetween(10,50),
            'date_start' => $this->faker->date,
            'date_end' => $this->faker->date,
            'active' => $this->faker->numberBetween(0,1),
        ];
    }
}
