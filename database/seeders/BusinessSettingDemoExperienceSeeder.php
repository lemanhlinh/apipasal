<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessSettingDemoExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\BusinessSettingDemoExperience::factory(10)->create();
    }
}
