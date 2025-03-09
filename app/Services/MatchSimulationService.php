<?php

namespace App\Services;

use App\Enums\MatchPointsEnum;
use App\Enums\MatchResultEnum;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MatchSimulationService
{

    private const HOME_ADVANTAGE = 10;
    private const RANDOM_FACTOR_MIN = -10;
    private const RANDOM_FACTOR_MAX = 10;
    private const HOME_DIVISOR = 15;
    private const AWAY_DIVISOR = 20;
    private const MAX_HOME_GOALS = 6;
    private const MAX_AWAY_GOALS = 5;
    private const MIN_GOALS = 0;

    /**
     * Simulate a match between two teams
     *
     * @param Team $homeTeam
     * @param Team $awayTeam
     * @return array
     */
    private function simulateMatch(Team $homeTeam, Team $awayTeam): array
    {
        // Get team powers
        $homePower = $homeTeam->power;
        $awayPower = $awayTeam->power;
        
        // Apply home advantage and random factor
        $randomFactor = rand(self::RANDOM_FACTOR_MIN, self::RANDOM_FACTOR_MAX);
 
        // Calculate scores
        $homeScore = floor(($homePower + self::HOME_ADVANTAGE + $randomFactor) / self::HOME_DIVISOR);
        $awayScore = floor(($awayPower + $randomFactor) / self::AWAY_DIVISOR);
        
        // Ensure scores are within reasonable limits
        $homeScore = min(max($homeScore, self::MIN_GOALS), self::MAX_HOME_GOALS);
        $awayScore = min(max($awayScore, self::MIN_GOALS), self::MAX_AWAY_GOALS);

        return [
            'home_team_goals' => $homeScore,
            'away_team_goals' => $awayScore,
        ];
    }

    /**
     * Determine match result based on goals scored
     *
     * @param int $homeTeamGoals
     * @param int $awayTeamGoals
     * @return array
     */
    private function getMatchResult(int $homeTeamGoals, int $awayTeamGoals): array
    {
        if ($homeTeamGoals > $awayTeamGoals) {
            return [
                MatchResultEnum::HOME_POINTS->value => MatchPointsEnum::WIN->value,
                MatchResultEnum::AWAY_POINTS->value => MatchPointsEnum::LOSS->value
            ];
        } elseif ($homeTeamGoals < $awayTeamGoals) {
            return [
                MatchResultEnum::HOME_POINTS->value => MatchPointsEnum::LOSS->value,
                MatchResultEnum::AWAY_POINTS->value => MatchPointsEnum::WIN->value
            ];
        } else {
            return [
                MatchResultEnum::HOME_POINTS->value => MatchPointsEnum::DRAW->value,
                MatchResultEnum::AWAY_POINTS->value => MatchPointsEnum::DRAW->value
            ];
        }
    }

    /**
     * Simulate match and update fixture with results
     *
     * @param object $fixture
     * @return array
     */
    private function simulateFixture($fixture): array
    {
        // Validate required relationships are loaded
        if (!$fixture->relationLoaded('homeTeam') || !$fixture->relationLoaded('awayTeam')) {
            throw new \InvalidArgumentException('Fixture must have homeTeam and awayTeam relationships loaded');
        }

        // Simulate match and get results
        $simulation = $this->simulateMatch($fixture->homeTeam, $fixture->awayTeam);
        $result = $this->getMatchResult($simulation['home_team_goals'], $simulation['away_team_goals']);

        // Update fixture with results
        $fixture->update([
            'home_team_score' => $simulation['home_team_goals'],
            'away_team_score' => $simulation['away_team_goals'],
            'home_team_points' => $result[MatchResultEnum::HOME_POINTS->value],
            'away_team_points' => $result[MatchResultEnum::AWAY_POINTS->value],
            'played' => true,
            'played_at' => Carbon::now(),
        ]);

        return [
            'fixture_id' => $fixture->id,
            'home_team' => $fixture->homeTeam->name,
            'away_team' => $fixture->awayTeam->name,
            'home_score' => $simulation['home_team_goals'],
            'away_score' => $simulation['away_team_goals'],
            'result' => $this->getResultSummary($simulation['home_team_goals'], $simulation['away_team_goals'])
        ];
    }

    /**
     * Get a human-readable result summary
     * 
     * @param int $homeTeamGoals
     * @param int $awayTeamGoals
     * @return string
     */
    private function getResultSummary(int $homeTeamGoals, int $awayTeamGoals): string
    {
        if ($homeTeamGoals > $awayTeamGoals) {
            return 'Home win';
        } elseif ($homeTeamGoals < $awayTeamGoals) {
            return 'Away win';
        } else {
            return 'Draw';
        }
    }

    /**
     * Simulate all matches in a match week
     *
     * @param Collection $fixtures
     * @return array
     */
    public function simulateMatchWeek(Collection $fixtures): array
    {
        // Eager load team relationships to avoid N+1 queries
        $fixtures->load(['homeTeam', 'awayTeam']);
        
        return $fixtures->map(function ($fixture) {
            return $this->simulateFixture($fixture);
        })->toArray();
    }
}