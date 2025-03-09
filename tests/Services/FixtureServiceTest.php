<?php

namespace Tests\Services;

use App\Services\FixtureService;
use PHPUnit\Framework\TestCase;

class FixtureServiceTest extends TestCase
{

    public function testGenerateFixture()
    {
        $fixtureService = new FixtureService();
        $this->assertEquals(
            'Generate Fixture',
            $fixtureService->generateFixture()
        );

    }
}
