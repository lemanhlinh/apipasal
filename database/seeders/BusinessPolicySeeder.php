<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\BusinessPolicy::factory(10)->create();
    }
}
