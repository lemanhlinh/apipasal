<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessSpendingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\BusinessSpending::factory(10)->create();
    }
}
