<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessMarketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $cityIds = City::pluck('code')->toArray();
        $cityId = $this->faker->randomElement($cityIds);
        $districtIds = District::where('city_code', $cityId)->pluck('code')->toArray();
        $districtId = $this->faker->randomElement($districtIds);

        return [
            'title' => $this->faker->name(),
            'segment' => $this->faker->numberBetween(0,4),
            'link_map' => $this->faker->url(),
            'city_id' => $cityId,
            'district_id' => $districtId,
            'potential' => $this->faker->numberBetween(0,2),
            'note' => $this->faker->realTextBetween($minNbChars = 160, $maxNbChars = 200, $indexSize = 2),
            'active' => $this->faker->numberBetween(0,1),
        ];
    }
}
