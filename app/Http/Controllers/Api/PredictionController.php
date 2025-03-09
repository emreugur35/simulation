<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PredictionResource;
use App\Models\Team;
use App\Services\PredictionService;

class PredictionController extends Controller
{
    private PredictionService $predictionService;

    /**
     * @param PredictionService $predictionService
     */
    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    public function prediction()
    {
        Team::all()->each(function ($team) use (&$teams) {
            $teams[$team->id]['id'] = $team->id;
            $teams[$team->id]['name'] = $team->name;
            $teams[$team->id]['prediction'] = round($this->predictionService->predictTeamPositionwithPercent($team), 2);
        });

        return response()->json(PredictionResource::collection(Collect($teams)->sortByDesc('prediction')));
    }


}
