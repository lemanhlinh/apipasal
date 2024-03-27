<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessSettingWareHouseRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\BusinessSettingWareHouseRule::factory(10)->create();
    }
}
