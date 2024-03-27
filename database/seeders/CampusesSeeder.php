<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CampusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Campuses::factory(10)->create()->each(function ($campuses) {
            \App\Models\CampusesClassroom::factory(rand(5,11))->create(['campuses_id' => $campuses->id]);
            \App\Models\BusinessPartner::factory(rand(5,11))->create(['campuses_id' => $campuses->id])->each(function ($partner){
                \App\Models\BusinessPartnerClue::factory(rand(5,11))->create(['partner_id' => $partner->id]);
            });
        });
    }
}
