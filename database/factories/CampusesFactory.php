<?php

namespace Database\Factories;

use App\Models\Campuses;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampusesFactory extends Factory
{
    protected $model = Campuses::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->name,
            'code' => $this->faker->regexify('[A-Z]{2}[0-4]{1} [A-Z]{2}'),
            'code_short' => $this->faker->regexify('[A-Z]{2}[0-4]{1}'),
            'type_campuses' => $this->faker->numberBetween(0,1),
            'active' => $this->faker->numberBetween(0,1),
        ];
    }
}
