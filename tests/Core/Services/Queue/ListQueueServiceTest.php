<?php

use ATCM\Core\Services\Aircraft\CreateAircraftService;
use ATCM\Core\Services\Flight\EnqueueAircraftService;
use ATCM\Core\Services\Flight\ListQueueService;
use ATCM\Core\Services\System\BootSystemService;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Flight;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\FlightType;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class ListQueueServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../../");
        $dotenv->load();

        BootSystemService::execute();
    }

    public function testExecute()
    {
        $this->createAircraft(FlightType::CARGO, AircraftSize::SMALL);
        $this->createAircraft(FlightType::VIP, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(FlightType::EMERGENCY, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(FlightType::VIP, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(FlightType::VIP, AircraftSize::LARGE);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(FlightType::CARGO, AircraftSize::SMALL);
        $this->createAircraft(FlightType::VIP, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(FlightType::EMERGENCY, AircraftSize::LARGE);
        $this->createAircraft(FlightType::CARGO, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(FlightType::VIP, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(FlightType::EMERGENCY, AircraftSize::SMALL);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(FlightType::VIP, AircraftSize::LARGE);
        $this->createAircraft(FlightType::PASSENGER, AircraftSize::LARGE);

        $queue = ListQueueService::execute();

        assertEquals(26, count($queue));
    }

    public function createAircraft($type, $size)
    {
        $aircraft = CreateAircraftService::execute(
            $size
        );
        
        EnqueueAircraftService::execute($aircraft->id, $type);
    }

    public function tearDown(): void
    {
        $queues = Flight::all();
        foreach($queues as $queue) {
            $queue->delete();
        }
        $aircrafts = Aircraft::all();
        foreach($aircrafts as $aircraft) {
            $aircraft->delete();
        }

    }

}