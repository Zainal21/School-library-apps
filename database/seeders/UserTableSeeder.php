<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'role' => 1,
                'name' => 'Admin'
        ]);
        User::create( [
            'email' => 'user@user.com',
            'password' => bcrypt('password'),
            'role' => 2,
            'name' => 'User'
        ]);
    }
}
