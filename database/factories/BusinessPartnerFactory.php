<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BusinessPartnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $typeCampuses = $this->faker->numberBetween(0, 1);
        $campusesId = $typeCampuses ? $this->faker->numberBetween(1, 10) : null;

        return [
            'title' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'type' => $this->faker->numberBetween(0,1),
            'segment' => $this->faker->numberBetween(0,4),
            'type_campuses' => $typeCampuses,
            'campuses_id' => $campusesId,
            'info_partner' => $this->faker->realTextBetween($minNbChars = 160, $maxNbChars = 200, $indexSize = 2),
            'active' => $this->faker->numberBetween(0,1),
        ];
    }
}
