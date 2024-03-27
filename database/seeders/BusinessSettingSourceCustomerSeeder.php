<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessSettingSourceCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\BusinessSettingSourceCustomer::factory(10)->create();
    }
}
