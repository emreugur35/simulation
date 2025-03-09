<?php

namespace Database\Factories;

use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fixture>
 */
class FixtureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $season = Season::factory()->create();
        $homeTeam = Team::factory()->forSeason($season->id)->create();
        $awayTeam = Team::factory()->forSeason($season->id)->create();
        
        return [
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'played_at' => null,
            'home_team_score' => null,
            'away_team_score' => null,
            'week' => $this->faker->numberBetween(1, $season->weeks),
            'played' => false,
            'season_id' => $season->id,
            'home_team_points' => null,
            'away_team_points' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    /**
     * Set specific week for the fixture.
     *
     * @param int $week
     * @return $this
     */
    public function inWeek(int $week)
    {
        return $this->state(function (array $attributes) use ($week) {
            return [
                'week' => $week,
            ];
        });
    }

    /**
     * Set specific teams for the fixture.
     *
     * @param int $homeTeamId
     * @param int $awayTeamId
     * @return $this
     */
    public function betweenTeams(int $homeTeamId, int $awayTeamId)
    {
        return $this->state(function (array $attributes) use ($homeTeamId, $awayTeamId) {
            return [
                'home_team_id' => $homeTeamId,
                'away_team_id' => $awayTeamId,
            ];
        });
    }

    /**
     * Set specific season for the fixture.
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

    /**
     * Mark the fixture as played with specific scores.
     *
     * @param int $homeScore
     * @param int $awayScore
     * @return $this
     */
    public function played(int $homeScore, int $awayScore)
    {
        return $this->state(function (array $attributes) use ($homeScore, $awayScore) {
            $homePoints = $homeScore > $awayScore ? 3 : ($homeScore == $awayScore ? 1 : 0);
            $awayPoints = $awayScore > $homeScore ? 3 : ($awayScore == $homeScore ? 1 : 0);
            
            return [
                'played' => true,
                'played_at' => Carbon::now()->subDays($this->faker->numberBetween(1, 30)),
                'home_team_score' => $homeScore,
                'away_team_score' => $awayScore,
                'home_team_points' => $homePoints,
                'away_team_points' => $awayPoints,
            ];
        });
    }

    /**
     * Schedule the fixture for a specific date.
     *
     * @param string|Carbon $playDate
     * @return $this
     */
    public function scheduledFor($playDate)
    {
        return $this->state(function (array $attributes) use ($playDate) {
            return [
                'played_at' => $playDate instanceof Carbon ? $playDate : Carbon::parse($playDate),
            ];
        });
    }
}