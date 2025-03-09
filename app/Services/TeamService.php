<?php

namespace App\Services;

use App\Models\Season;

class TeamService
{


    public function getTeamsinSeason(Season $season)
    {
        $teams = $season::with('teams')->get()->pluck('teams');
    }


}
