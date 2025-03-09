<?php

namespace App\Enums;

enum MatchResultEnum: string
{
    case HOME_SCORE = 'home_team_score';
    case AWAY_SCORE = 'away_team_score';
    case HOME_POINTS = 'home_team_points';
    case AWAY_POINTS = 'away_team_points';
}
