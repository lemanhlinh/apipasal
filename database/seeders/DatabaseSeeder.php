<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
//        $this->call(CampusesSeeder::class);
//        $this->call(DepartmentSeeder::class);
        $this->call(CampusesDepartmentSeeder::class);
        $this->call(RegenciesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CourseCategoriesSeeder::class);
        $this->call(CoursesSeeder::class);
        $this->call(ProductCategoriesSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(BusinessSettingSourceCustomerSeeder::class);
        $this->call(BusinessSettingDemoExperienceSeeder::class);
        $this->call(BusinessSettingWareHouseRuleSeeder::class);
        $this->call(BusinessSpendingSeeder::class);
    }
}
