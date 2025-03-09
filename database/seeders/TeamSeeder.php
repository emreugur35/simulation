<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Season;
use Faker\Factory as Faker;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $latestSeason = Season::latest()->first();

        for ($i = 0; $i < 18; $i++) {
            Team::create([
                'name' => $faker->company,
                'season_id' => $latestSeason->id,
                'power' => $faker->numberBetween(10, 70),
            ]);
        }
    }
}
