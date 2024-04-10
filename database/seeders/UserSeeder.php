<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(1)->create([
            'name' => 'Admin',
            'email' => 'admin@finalstyle.com',
            'phone' => '0123456789',
            'password' => bcrypt(123456),
            'active' => 1,
            'department_id' => 1,
            'regency_id' => 1,
        ]);

        \App\Models\User::factory(20)->create();
    }
}
