<?php
namespace Test\Data\Models;

use ATCM\Core\Helpers\AutoGenerateHelper;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\FlightType;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Queue;
use PHPUnit\Framework\TestCase;


use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertNotNull;


class QueueTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../");
        $dotenv->load();
    }

    public function testCreateQueue()
    {
        $aircraft = Aircraft::create([
            'size' => AircraftSize::LARGE,
            'model' => AutoGenerateHelper::generateModel(AircraftSize::LARGE)
        ]);
        $aircraft->save();
        
        $queue = Queue::create();
        $queue->aircraftId = $aircraft->id;
        $queue->flightType = FlightType::EMERGENCY;
        $queue->flightNumber = AutoGenerateHelper::generateFlight();
        $queue->save();

        assertIsInt($queue->id);

        //Queue::destroy($queue->id);
        //Aircraft::destroy($aircraft->id);
        $queue->delete();
        $aircraft->delete();

        assertEquals(0, Aircraft::count());
        assertEquals(0, Queue::count());

    }

    public function testAircraft()
    {
        
        $aircraft = Aircraft::create([
            'model' => 'Boeing 777',
            'size' => AircraftSize::LARGE,
        ]);
        $aircraft->save();
        
        $queue = new Queue();
        $queue->aircraftId = $aircraft->id;
        $queue->flightNumber = AutoGenerateHelper::generateFlight();
        $queue->flightType = FlightType::EMERGENCY;
        $queue->save();
        
        $aircraft2 = $queue->aircraft();
        assertNotNull($aircraft2);
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