<?php
namespace Test\Data\Models;

use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
use ATCM\Data\Models\Aircraft;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertObjectEquals;
use function PHPUnit\Framework\assertSame;

class AircraftTest extends TestCase
{
    private $aircraft;

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

        $aircraft->save();

        assertIsInt($aircraft->id);

        $this->aircraft = $aircraft;

        $aircraft2 = Aircraft::find($this->aircraft->id);
        assertEquals($this->aircraft->id, $aircraft2->id);
        assertEquals($this->aircraft->model, $aircraft2->model);
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

        $aircraft->save();

        assertIsInt($aircraft->id);

        $this->aircraft = $aircraft;

        $aircraft2 = Aircraft::find($this->aircraft->id);
        assertEquals($this->aircraft->id, $aircraft2->id);
        assertEquals($this->aircraft->model, $aircraft2->model);
    }

    public function testUpdateAircraft()
    {
        $number = random_int(1000,9999);
        $aircraft = new Aircraft();
        $aircraft->type = AircraftType::EMERGENCY;
        $aircraft->size = AircraftSize::SMALL;
        $aircraft->model = "Boeing 747";
        $aircraft->flightNumber = "BRA" . $number;
        $id = $aircraft->save()->id;

        unset($aircraft);

        $aircraft2 = Aircraft::find($id);
        assertNotNull($aircraft2);
        assertEquals($id, $aircraft2->id);
    }

    public function testDeleteAircraft()
    {
        $number = random_int(1000,9999);
        $aircraft = new Aircraft();
        $aircraft->type = AircraftType::EMERGENCY;
        $aircraft->size = AircraftSize::SMALL;
        $aircraft->model = "Boeing 747";
        $aircraft->flightNumber = "BRA" . $number;
        $id = $aircraft->save()->id;
        unset($aircraft);

        Aircraft::destroy($id);

        $aircraft2 = Aircraft::find($id);
        assertNull($aircraft2);
    }

    public function testAllAircrafts()
    {
        $aircrafts = Aircraft::all();

        assertEquals(36, count($aircrafts));
        assertEquals('BRA2569', $aircrafts[0]->flightNumber);
    }

}