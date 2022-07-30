<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Author;

class AuthorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        for ($i = 0; $i < 10; $i++) {
            Author::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'short_description' => 'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Neque, tenetur!',
                'date_of_birth' => $faker->date(),
                'created_at' => $faker->date()
            ]);
        }
    }
}
