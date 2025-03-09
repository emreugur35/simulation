<?php

namespace Database\Factories;

use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'power' => $this->faker->numberBetween(50, 100),
            'season_id' => Season::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the team with a specific power rating.
     *
     * @param int $power
     * @return $this
     */
    public function withPower(int $power)
    {
        return $this->state(function (array $attributes) use ($power) {
            return [
                'power' => $power,
            ];
        });
    }

    /**
     * Configure the team with an existing season.
     *
     * @param int $seasonId
     * @return $this
     */
    public function forSeason(int $seasonId)
    {
        return $this->state(function (array $attributes) use ($seasonId) {
            return [
                'season_id' => $seasonId,
            ];
        });
    }
}