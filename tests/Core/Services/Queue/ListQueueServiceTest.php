<?php

use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Services\Aircraft\CreateAircraftService;
use ATCM\Core\Services\Queue\EnqueueAircraftService;
use ATCM\Core\Services\Queue\ListQueueService;
use ATCM\Core\Services\System\BootSystemService;
use ATCM\Core\Services\System\HaltSystemService;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Queue;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
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
        $this->createAircraft(AircraftType::CARGO, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::VIP, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::EMERGENCY, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::VIP, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::VIP, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::CARGO, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::VIP, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::EMERGENCY, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::CARGO, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::VIP, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::EMERGENCY, AircraftSize::SMALL);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::VIP, AircraftSize::LARGE);
        $this->createAircraft(AircraftType::PASSENGER, AircraftSize::LARGE);

        $queue = ListQueueService::execute();
        
        $newArray = [];
        foreach($queue as $item) {
            $newArray[] = [$item->toArray(), ['enqueued_at' => $item->createdAt]];
        }
        file_put_contents(__DIR__."/../../../results/listQueue.txt", json_encode($newArray));
        assertEquals(26, count($queue));
        assertEquals(AircraftType::EMERGENCY, $queue[0]->type);
        assertEquals(AircraftSize::LARGE, $queue[0]->size);
    }

    public function createAircraft($type, $size)
    {
        $models = ["Embraer 190", "Airbus A330", "Boeing 747", "Learjet Legacy", "Embraer KC390", "Cessna C95", "Lockheed C130"];
        $model = $models[random_int(0,count($models) - 1)];

        $random = random_int(1000,9999);
        $aircraft = CreateAircraftService::execute(
            $type,
            $size,
            chr(rand(65,90)).chr(rand(65,90)).chr(rand(65,90)).$random,
            $model
        );
        
        EnqueueAircraftService::execute($aircraft->id);
    }

    public function tearDown(): void
    {
        $queues = Queue::all();
        foreach($queues as $queue) {
            $queue->delete();
        }
        $aircrafts = Aircraft::all();
        foreach($aircrafts as $aircraft) {
            $aircraft->delete();
        }

    }

}