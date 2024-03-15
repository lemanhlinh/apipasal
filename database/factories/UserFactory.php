<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt(123456),
            'remember_token' => Str::random(10),
            'phone' => $this->faker->phoneNumber(),
            'birthday' => $this->faker->date,
            'image' => $this->faker->imageUrl(640, 480, 'pasal', true),
            'active' => $this->faker->numberBetween(0,1),
            'department_id' => $this->faker->numberBetween(1,10),
            'regency_id' => $this->faker->numberBetween(1,10),
            'used_time' => now(),
        ];
    }
}
