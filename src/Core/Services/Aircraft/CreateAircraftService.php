<?php

namespace ATCM\Core\Services\Aircraft;

use ATCM\Data\Models\Aircraft;
use LogicException;

/**
 * Execute a verification if system is online, returning the status acording SystemStatus enum
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class CreateAircraftService
{
    public static function execute(string $type, string $size, string $flightNumber, string $model)
    {
        $existingAircraft = Aircraft::first("flight_number LIKE '{$flightNumber}'");
        if(!is_null($existingAircraft)) throw new LogicException(sprintf("It already exists a aircraft with %s flight number", $flightNumber));

        $aircraft = new Aircraft();
        $aircraft->type = $type;
        $aircraft->size = $size;
        $aircraft->flightNumber = $flightNumber;
        $aircraft->model = $model;
        $aircraft->save();

        return $aircraft;
    }
}
