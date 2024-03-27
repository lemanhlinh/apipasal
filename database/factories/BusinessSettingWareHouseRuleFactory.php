<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessSettingWareHouseRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'count_day' => $this->faker->numberBetween(1,10),
            'time_in_warehouse' => $this->faker->date,
        ];
    }
}
