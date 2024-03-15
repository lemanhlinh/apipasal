<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CourseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\CourseCategories::factory(10)->create();
    }
}
