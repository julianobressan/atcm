<?php
namespace Test\Data\Models;

use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Queue;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertObjectEquals;
use function PHPUnit\Framework\assertSame;

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
            'model' => 'Boeing 777',
            'flightNumber' => 'BRA9878',
            'size' => AircraftSize::LARGE,
            'type' => AircraftType::VIP
        ]);
        $aircraft->save();
        
        $queue = Queue::create();
        $queue->aircraftId = $aircraft->id;
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
            'flightNumber' => 'BRA9878',
            'size' => AircraftSize::LARGE,
            'type' => AircraftType::VIP
        ]);
        $aircraft->save();
        
        $queue = new Queue();
        $queue->aircraftId = $aircraft->id;
        $queue->save();
        
        $aircraft2 = $queue->aircraft();
        assertNotNull($aircraft2);
        assertEquals('BRA9878', $aircraft2->flightNumber);
    }

    public function tearDown(): void
    {
        $aircrafts = Aircraft::all();
        foreach($aircrafts as $aircraft) {
            $aircraft->delete();
        }

        $queues = Queue::all();
        foreach($queues as $queue) {
            $queue->delete();
        }
    }
}