<?php


use ATCM\Core\Services\Aircraft\CreateAircraftService;
use ATCM\Core\Services\Flight\DequeueService;
use ATCM\Core\Services\Flight\EnqueueAircraftService;
use ATCM\Core\Services\Flight\ListQueueService;
use ATCM\Core\Services\System\BootSystemService;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Flight;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\FlightType;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class DequeueServiceTest extends TestCase
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
        

        $queue = ListQueueService::execute();
        assertEquals(12, count($queue));

        $queue2 = ListQueueService::execute();
        DequeueService::execute($queue2[0]['flight']->id);
        $queue3 = ListQueueService::execute();
        assertEquals(11, count($queue3));
    }

    public function createAircraft($type, $size)
    {
        $models = ["Embraer 190", "Airbus A330", "Boeing 747", "Learjet Legacy", "Embraer KC390", "Cessna C95", "Lockheed C130"];
        $model = $models[random_int(0,count($models) - 1)];

        $aircraft = CreateAircraftService::execute(
            $size,
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