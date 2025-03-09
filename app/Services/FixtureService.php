<?php

namespace App\Services;

use App\Exceptions\FixtureException;
use App\Models\Fixture;
use App\Models\Season;
use Exception;

class FixtureService
{

    /**
     * @param $teams
     * @param $season
     * @return array|string
     * @throws Exception
     */
    public function generateFixture(Season $season): array|string
    {
        $teams = Season::with('teams')->get()->pluck('teams')->first()->toArray();

        try {

            $numTeams = count($teams);

            $midpoint = $numTeams;

            $weeks = ($numTeams - 1) * 2;

            for ($i = 0; $i < $weeks; $i++) {
                $weekMatchesByTeam = [];

                for ($j = 0; $j < $midpoint; $j++) {
                    $homeIndex = ($i + $j) % ($numTeams - 1);
                    $awayIndex = ($numTeams - 1 - $j + $i) % ($numTeams - 1);

                    if ($j == 0) {
                        $awayIndex = $numTeams - 1;
                    }


                    $homeTeam = $teams[$homeIndex];
                    $awayTeam = $teams[$awayIndex];

                    if ($homeTeam['id'] === $awayTeam['id']) {
                        continue;
                    }

                    if (!isset($weekMatchesByTeam[$homeTeam['id']]) && !isset($weekMatchesByTeam[$awayTeam['id']])) {

                        $weekMatchesByTeam[$homeTeam['id']] = true;
                        $weekMatchesByTeam[$awayTeam['id']] = true;



                        Fixture::create([
                            'home_team_id' => $homeTeam['id'],
                            'away_team_id' => $awayTeam['id'],
                            'week' => $i + 1,
                            'played' => 0,
                            'season_id' => $season->id
                        ]);

                    }
                }
            }

            return ['message' => 'Fixture generated'];


        } catch (FixtureException $e) {
            return $e->getMessage();
        }
    }


}
