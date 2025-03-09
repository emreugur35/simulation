<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Services\MatchSimulationService;
use App\Services\PredictionService;
use Illuminate\Console\Command;

class simulateCommand extends Command
{

    public function __construct(private PredictionService $predictionService)
    {

        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $teams = Team::all();

        foreach($teams as $team){
            echo $this->predictionService->predictTeamPositionwithPercent($team);
        }




    }
}
