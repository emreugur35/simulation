<?php

namespace App\Services;

use App\Models\Team;

class PredictionService
{

    public function predictTeamPositionwithPercent(Team $targetTeam)
    {
        $teams = Team::all();
        $targetPoints = $this->predictTeamOutcome($targetTeam);
        $position = 1;

        foreach ($teams as $otherTeam) {
            if ($otherTeam->id !== $targetTeam->id) {
                $otherPoints = $this->predictTeamOutcome($otherTeam);
                if ($otherPoints > $targetPoints) {
                    $position++;
                }
            }
        }

        $totalTeams = count($teams);
        $winningPercent = 100 - (($position - 1) / ($totalTeams - 1) * 100);

        return $winningPercent;
    }


    public function predictTeamOutcome($team)
    {
        $matches = $team->fixturesHome->merge($team->fixturesAway);

        $points = 0;
        foreach ($matches as $match) {
            $points += $this->predictMatchOutcome($team, $match);
        }
        return $points;
    }

    public function predictMatchOutcome($team, $match)
    {
        $homeScore = $match->home_team_score;
        $awayScore = $match->away_team_score;
        $homePoints = $match->home_team_points;
        $awayPoints = $match->away_team_points;
        if ($team->id == $match->home_team_id) {
            if ($homeScore > $awayScore) {
                return $homePoints;
            } elseif ($homeScore == $awayScore) {
                return $homePoints / 2;
            } else {
                return 0;
            }
        } else {
            if ($awayScore > $homeScore) {
                return $awayPoints;
            } elseif ($awayScore == $homeScore) {
                return $awayPoints / 2;
            } else {
                return 0;
            }
        }
    }


}
