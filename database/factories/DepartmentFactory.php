<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->streetName,
            'code' => $this->faker->regexify('[A-Z]{2}'),
            'type_office' => $this->faker->numberBetween(0,1),
            'active' => $this->faker->numberBetween(0,1),
            'user_id' => 1,
        ];
    }
}
