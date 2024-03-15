<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CoursesFactory extends Factory
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
            'code' => $this->faker->regexify('[A-Z]{2}'),
            'number_course' => $this->faker->numberBetween(3,10),
            'active' => $this->faker->numberBetween(0,1),
        ];
    }
}
