<?php

namespace Tests\Services;

use App\Services\MatchSimulationService;
use PHPUnit\Framework\TestCase;

class MatchSimulationServiceTest extends TestCase
{

    public function testSimulateMatchWeek()
    {
        $matchSimulationService = new MatchSimulationService();
        $this->assertEquals(
            'Simulate Match Week',
            $matchSimulationService->simulateMatchWeek()
        );

    }
}
