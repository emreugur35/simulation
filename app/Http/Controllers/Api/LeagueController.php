<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\FixtureException;
use App\Http\Controllers\Controller;
use App\Http\Resources\FixtureResource;
use App\Http\Resources\TeamResource;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use App\Services\FixtureService;
use App\Services\MatchSimulationService;

class LeagueController extends Controller
{
    private FixtureService $fixtureService;
    private MatchSimulationService $matchSimulationService;

    /**
     * @param FixtureService $fixtureService
     */
    public function __construct(FixtureService $fixtureService, MatchSimulationService $matchSimulationService)
    {
        $this->fixtureService = $fixtureService;
        $this->matchSimulationService = $matchSimulationService;
    }

    public function getTeamsinSeason(Season $season)
    {
        $teams = Team::where('season_id', $season->id ?? 1)
            ->selectRaw('teams.*,
    COALESCE((SELECT SUM(home_team_points) FROM fixtures WHERE home_team_id = teams.id AND played = 1), 0) +
    COALESCE((SELECT SUM(away_team_points) FROM fixtures WHERE away_team_id = teams.id AND played = 1), 0) as points')
            ->orderBy('points', 'desc')
            ->get();

        return response()->json(TeamResource::collection($teams));
    }

    public function generateFixtures()
    {

        $season = Season::where('completed', 0)->firstOrFail();

        $result = $this->fixtureService->generateFixture($season);

        return response()->json($result);
    }

    public function playAll(): \Illuminate\Http\JsonResponse
    {
        $fixtures = Fixture::where('played', 0)->get();
        $this->matchSimulationService->simulateMatchWeek($fixtures);
        $season = Season::where('completed', 0)->first();
        if ($season) {
            $season->completed = 1;
            $season->current_week = $season->weeks;
            $season->save();
        }

        return response()->json(['message' => 'All fixtures played']);
    }

    public function playNextWeek(): \Illuminate\Http\JsonResponse
    {
        try {

            $season = Season::where('completed', 0)->firstOrFail();
            $fixture = Fixture::where(['played' => 0, 'week' => $season->current_week + 1])->orderBy('week')->get();
            $this->matchSimulationService->simulateMatchWeek($fixture);
            $season->current_week = $season->current_week + 1;
            $season->save();
            return response()->json(['message' => 'Week played']);

        } catch (FixtureException $e) {
            return response()->json(['message' => 'No fixtures to play']);
        }
    }

    public function getCurrentWeek(): \Illuminate\Http\JsonResponse
    {
        try {
            $season = Season::firstOrFail();
            $fixture = Fixture::where(['week' => $season->current_week])->orderBy('week')->get();
            return response()->json(FixtureResource::collection($fixture));

        } catch (FixtureException $e) {

            return response()->json(['message' => $e->getMessage()]);

        }

    }

    public function resetLeague(): \Illuminate\Http\JsonResponse
    {
        Fixture::truncate();
        $season = Season::firstOrFail();
        $season->current_week = 0;
        $season->completed = 0;
        $season->save();
        return response()->json(['message' => 'League reset']);
    }


    public function getFixtures(): \Illuminate\Http\JsonResponse
    {
        try {
            $season = Season::where('completed', 0)->firstOrFail();
            if ($season->current_week == $season->weeks) {
                throw new FixtureException('Season Completed');
            }

            $fixture = Fixture::get();
            return response()->json(FixtureResource::collection($fixture));

        } catch (FixtureException $e) {

            return response()->json(['message' => $e->getMessage()]);

        }

    }

    public function status()
    {
        $fixtureCount = Fixture::count();
        $pendingFixtureCount = Fixture::where('played', 0)->count();

        if ($fixtureCount === 0) {
            return response()->json(['step' => '']);
        }

        if ($pendingFixtureCount === 0) {
            return response()->json(['step' => 'finished']);
        }

        if ($pendingFixtureCount > 0) {
            return response()->json(['step' => 'fixtures']);
        }

        return response()->json(['step' => 'started']);
    }

}
