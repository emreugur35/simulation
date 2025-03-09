<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'home_team' => $this->homeTeam->name,
            'away_team' => $this->awayTeam->name,
            'home_team_goals' => $this->home_team_score ?? '-',
            'away_team_goals' => $this->away_team_score ?? '-',
            'played_at' => $this->played_at ?? '-',
            'is_played' => $this->is_played ?? 'No',
            'week'=>$this->week
        ];
    }
}
