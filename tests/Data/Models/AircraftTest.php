<?php
namespace Test\Data\Models;

use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
use ATCM\Data\Models\Aircraft;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertObjectEquals;
use function PHPUnit\Framework\assertSame;

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
        $aircraft->type = AircraftType::EMERGENCY;
        $aircraft->size = AircraftSize::SMALL;
        $aircraft->model = "Boeing 747";
        $aircraft->flightNumber = "BRA" . $number;

        assertEquals(Aircraft::class, get_class($aircraft));
        assertEquals(AircraftType::EMERGENCY, $aircraft->type);
        assertEquals(AircraftSize::SMALL, $aircraft->size);
        assertEquals("Boeing 747", $aircraft->model);
        assertEquals("BRA" . $number, $aircraft->flightNumber);

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
        $aircraft->type = AircraftType::EMERGENCY;
        $aircraft->size = AircraftSize::SMALL;
        $aircraft->model = "Boeing 747";
        $aircraft->flightNumber = "BRA" . $number;

        assertEquals(Aircraft::class, get_class($aircraft));
        assertEquals(AircraftType::EMERGENCY, $aircraft->type);
        assertEquals(AircraftSize::SMALL, $aircraft->size);
        assertEquals("Boeing 747", $aircraft->model);
        assertEquals("BRA" . $number, $aircraft->flightNumber);

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
        $aircrafts = Aircraft::all();

        assertEquals(2, count($aircrafts));
        assertEquals('BRA', substr($aircrafts[0]->flightNumber, 0, 3));
    }

    public function testUpdateAircraft()
    {
        $aircraft = Aircraft::all('', [], 1)[0];
        $aircraft->model = "Airbus A330";
        $aircraft->save();

        $aircraft2 = Aircraft::find($aircraft->id);
        assertEquals("Airbus A330", $aircraft2->model);
    }

    public function testDeleteAircraft()
    {        
        $aircrafts = Aircraft::all();
        assertEquals(2, count($aircrafts));
        $fisrAircraft = array_shift($aircrafts);
        $fisrAircraft->delete();

        $aircrafts2 = Aircraft::all();
        assertEquals(1, count($aircrafts2));

        foreach($aircrafts as $aircraft) {
            Aircraft::destroy($aircraft->id);
        }
        $aircrafts3 = Aircraft::all();
        assertEmpty($aircrafts3);
    }

   

}