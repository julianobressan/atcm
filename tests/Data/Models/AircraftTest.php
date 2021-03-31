<?php

use ATCM\Core\Helpers\AutoGenerateHelper;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
use ATCM\Data\Enums\FlightType;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Flight;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsInt;

class AircraftTest extends TestCase
{
    private $aircrafts = [];

    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../");
        $dotenv->load();
    }

    public function testCreateNewAircraft()
    {
        $number = random_int(1000,9999);

        $aircraft = new Aircraft();
        $aircraft->size = AircraftSize::SMALL;
        $aircraft->model = "Boeing 747";

        assertEquals(Aircraft::class, get_class($aircraft));
        assertEquals(AircraftSize::SMALL, $aircraft->size);
        assertEquals("Boeing 747", $aircraft->model);

        $id = $aircraft->save()->id;
        assertIsInt($aircraft->id);
        $this->aircrafts[$id] = $aircraft;

        $aircraft2 = Aircraft::find($id);
        assertEquals($this->aircrafts[$id]->id, $aircraft2->id);
        assertEquals($this->aircrafts[$id]->model, $aircraft2->model);
    }

    public function testFindAircraft()
    {
        $number = random_int(1000,9999);

        $aircraft = new Aircraft();
        $aircraft->size = AircraftSize::SMALL;
        $aircraft->model = "Boeing 747";

        assertEquals(Aircraft::class, get_class($aircraft));
        assertEquals(AircraftSize::SMALL, $aircraft->size);
        assertEquals("Boeing 747", $aircraft->model);

        $id = $aircraft->save()->id;
        assertIsInt($aircraft->id);
        $this->aircrafts[$id] = $aircraft;
        unset($aircraft);

        $aircraft2 = Aircraft::find($this->aircrafts[$id]->id);
        assertEquals($this->aircrafts[$id]->id, $aircraft2->id);
        assertEquals($this->aircrafts[$id]->model, $aircraft2->model);
    }   

    public function testAllAircrafts()
    {
        $this->createAircraft();
        $this->createAircraft();
        $this->createAircraft();

        $aircrafts = Aircraft::all();

        assertEquals(3, count($aircrafts));
    }

    public function testUpdateAircraft()
    {
        $this->createAircraft();

        $aircraft2 = Aircraft::all('', [], 1)[0];
        $aircraft2->model = "Airbus A330";
        $aircraft2->save();

        $aircraft3 = Aircraft::find($aircraft2->id);
        assertEquals("Airbus A330", $aircraft3->model);
    }

    public function testCountAircraft()
    { 
        $this->createAircraft();
        $this->createAircraft();
        $this->createAircraft();

        $count = Aircraft::count();
        assertEquals(3, $count);
    }

    public function testDeleteAircraft()
    {        
        $this->createAircraft();
        $this->createAircraft();
        $this->createAircraft();

        $aircrafts = Aircraft::all();

        assertEquals(3, count($aircrafts));
        $fisrAircraft = array_shift($aircrafts);
        $fisrAircraft->delete();

        $aircrafts2 = Aircraft::all();
        assertEquals(2, count($aircrafts2));

        foreach($aircrafts as $aircraft) {
            Aircraft::destroy($aircraft->id);
        }
        $aircrafts3 = Aircraft::all();
        assertEmpty($aircrafts3);
    }

    public function testEnqueued()
    {
        $aircraft = $this->createAircraft();

        $queue1 = new Flight();
        $queue1->aircraftId = $aircraft->id;
        $queue1->flightNumber = AutoGenerateHelper::generateFlight();
        $queue1->flightType = FlightType::EMERGENCY;
        $queue1->save();

        $queue2 = new Flight();
        $queue2->aircraftId = $aircraft->id;
        $queue2->flightNumber = AutoGenerateHelper::generateFlight();
        $queue2->flightType = FlightType::EMERGENCY;
        $queue2->save();

        assertEquals(2, count($aircraft->flights()));
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

    private function createAircraft()
    {
        $aircraft = new Aircraft();
        $aircraft->size = AircraftSize::SMALL;
        $aircraft->model = "Boeing 747";
        $aircraft->save();
        return $aircraft;
    }
}