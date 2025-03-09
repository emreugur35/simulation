<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Season;
use Faker\Factory as Faker;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $year = $faker->year;

        Season::create([
            'completed' => 0,
            'name' => 'Season ' . $year,
            'current_week' => 0,
            'weeks' => 38
        ]);
    }
}
