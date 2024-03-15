<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RegenciesFactory extends Factory
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
            'code' => $this->faker->regexify('[A-Z]{2}'),
            'active' => $this->faker->numberBetween(0,1),
            'department_id' => $this->faker->numberBetween(1,10),
            'permission' => $this->faker->sentence,
        ];
    }
}
