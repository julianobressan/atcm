<?php

use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
use ATCM\Data\Models\Aircraft;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class AircraftTest extends TestCase
{
    public function testCreateNewAircraft()
    {
        $aircraft = new Aircraft();
        $aircraft->type = AircraftType::EMERGENCY;
        $aircraft->size = AircraftSize::SMALL;
        $aircraft->model = "Boeing 747";
        $aircraft->flightNumber = "BRA2569";

        assertEquals(Aircraft::class, get_class($aircraft));
        assertEquals(AircraftType::EMERGENCY, $aircraft->type);
        assertEquals(AircraftSize::SMALL, $aircraft->size);
        assertEquals("Boeing 747", $aircraft->model);
        assertEquals("BRA2569", $aircraft->flightNumber);
    }
}