<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'season_id',
        'week',
        'played',
        'played_at',
        'home_team_score',
        'away_team_score',
        'home_team_points',
        'away_team_points'
    ];

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');

    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');

    }


}
