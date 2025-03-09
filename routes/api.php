<?php

use App\Http\Controllers\Api\LeagueController;
use App\Http\Controllers\Api\PredictionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/start-new-league', [LeagueController::class, 'generateFixtures']);
Route::get('/fixtures', [LeagueController::class, 'getFixtures']);
Route::get('/reset-league', [LeagueController::class, 'resetLeague']);
Route::get('/play-all', [LeagueController::class, 'playAll']);
Route::get('/play-next-week', [LeagueController::class, 'playNextWeek']);
Route::get('/current-week', [LeagueController::class, 'getCurrentWeek']);
Route::get('/league-teams', [LeagueController::class, 'getTeamsinSeason']);
Route::get('/league-predictions', [PredictionController::class, 'prediction']);
Route::get('/status', [LeagueController::class, 'status']);
