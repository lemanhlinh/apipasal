<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Regencies::factory(10)->create();
    }
}
