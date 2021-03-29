<?php

use ATCM\Core\Services\Aircraft\CreateAircraftService;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

class CreateAircraftServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../../");
        $dotenv->load();
    }

    public function testExecute()
    {
        $number = random_int(1000,9999);
        $aircraft = CreateAircraftService::execute(AircraftType::CARGO, AircraftSize::LARGE, "BRA" . $number, "Boeing 757");
        assertNotNull($aircraft);
    }

    public function tearDown(): void
    {
        $aircrafts = Aircraft::all();
        foreach($aircrafts as $aircraft) {
            $aircraft->delete();
        }

    }

}