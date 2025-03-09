<?php

namespace App\Console\Commands;

use App\Models\League;
use App\Services\FixtureService;
use Illuminate\Console\Command;

class TestCommand extends Command
{

    public function __construct(FixtureService $fixtureService)
    {
        $this->fixtureService = $fixtureService;
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-command';

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
        $league = League::find(1);
        $fixtures = $this->fixtureService->generateFixture($league);
      //  dd($fixtures);

    }
}
