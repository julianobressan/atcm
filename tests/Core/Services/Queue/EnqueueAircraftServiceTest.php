<?php

use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Services\Aircraft\CreateAircraftService;
use ATCM\Core\Services\Queue\EnqueueAircraftService;
use ATCM\Core\Services\System\BootSystemService;
use ATCM\Core\Services\System\HaltSystemService;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Queue;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class EnqueueAircraftServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../../");
        $dotenv->load();

        BootSystemService::execute();
    }

    public function testExecute()
    {
        $aircraft = $this->createAircraft();

        EnqueueAircraftService::execute($aircraft->id);
        assertEquals(1, count($aircraft->enqueued()));

        $this->expectException(NotAllowedException::class);
        EnqueueAircraftService::execute($aircraft->id);
    }

    public function testExecuteWithHaltedSystem()
    {
        $aircraft = $this->createAircraft();

        HaltSystemService::execute();

        $this->expectException(NotAllowedException::class);

        EnqueueAircraftService::execute($aircraft->id);       
    }

    public function createAircraft()
    {
        $random = random_int(1000,9999);
        $aircraft = CreateAircraftService::execute(
            AircraftType::CARGO,
            AircraftSize::LARGE,
            "BRA".$random,
            "Embraer 190"
        );
        return $aircraft;
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