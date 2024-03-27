<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CampusesDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\CampusesDepartment::factory(10)->create([
            'campuses_id' => function () {
                $campus = \App\Models\Campuses::factory()->create();
                \App\Models\CampusesClassroom::factory(rand(5,11))->create(['campuses_id' => $campus->id]);
                \App\Models\BusinessPartner::factory(rand(5,11))->create(['campuses_id' => $campus->id])->each(function ($partner){
                    \App\Models\BusinessPartnerClue::factory(rand(5,11))->create(['partner_id' => $partner->id]);
                });
                return $campus->id;
            },
            'department_id' => \App\Models\Department::factory(),
        ]);
    }
}
