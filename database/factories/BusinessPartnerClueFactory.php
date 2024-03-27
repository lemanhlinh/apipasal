<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessPartnerClueFactory extends Factory
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
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'position' => $this->faker->name(),
            'partner_id' => $this->faker->numberBetween(1,10),
            'birthday' => $this->faker->date,
            'active' => $this->faker->numberBetween(0,1),
        ];
    }
}
