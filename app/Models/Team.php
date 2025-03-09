<?php

namespace App\Models;

use App\Enums\MatchPointsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function fixturesHome()
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    public function fixturesAway()
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }



    public function scopeLosses()
    {
        return $this->fixturesHome()->where('played', 1)
                ->where(function ($query) {
                    $query->where('home_team_id', $this->id)
                        ->where('home_team_points', '=', MatchPointsEnum::LOSS->value);
                })->count() + $this->fixturesAway()->where('played', 1)
                ->where(function ($query) {
                    $query->where('away_team_id', $this->id)
                        ->where('away_team_points', '=', MatchPointsEnum::LOSS->value);
                })->count();

    }

    public function scopeWins()
    {
        return $this->fixturesHome()->where('played', 1)
                ->where(function ($query) {
                    $query->where('home_team_id', $this->id)
                        ->where('home_team_points', '=', MatchPointsEnum::WIN->value);
                })->count() + $this->fixturesAway()->where('played', 1)
                ->where(function ($query) {
                    $query->where('away_team_id', $this->id)
                        ->where('away_team_points', '=', MatchPointsEnum::WIN->value);
                })->count();

    }

    public function scopeDraws()
    {
        return $this->fixturesHome()->where('played', 1)
                ->where(function ($query) {
                    $query->where('home_team_id', $this->id)
                        ->where('home_team_points', '=', MatchPointsEnum::DRAW->value);
                })->count() + $this->fixturesAway()->where('played', 1)
                ->where(function ($query) {
                    $query->where('away_team_id', $this->id)
                        ->where('away_team_points', '=', MatchPointsEnum::DRAW->value);
                })->count();

    }

    public function scopeAverage()
    {
        return $this->fixturesHome()->where('played', 1)
                ->where('home_team_id', $this->id)
                ->sum('home_team_score') +
                $this->fixturesAway()->where('played', 1)
                ->where('away_team_id', $this->id)
                ->sum('away_team_score');
    }

    public function scopePoints()
    {
        return $this->fixturesHome()->where('played', 1)
                ->where('home_team_id', $this->id)
                ->sum('home_team_points') +
            $this->fixturesAway()->where('played', 1)
                ->where('away_team_id', $this->id)
                ->sum('away_team_points');
    }






}
