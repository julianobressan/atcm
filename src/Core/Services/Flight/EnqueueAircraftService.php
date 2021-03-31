<?php

namespace ATCM\Core\Services\Flight;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Helpers\AutoGenerateHelper;
use ATCM\Core\Interfaces\IService;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Data\Enums\FlightType;
use ATCM\Data\Enums\SystemStatus;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Flight;

/**
 * Add an aircraft to queue
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class EnqueueAircraftService implements IService
{
    public static function execute($aircraftId = null, $flightType = null, $flightNumber = null)
    {
        if(empty($aircraftId)) {
            throw new InvalidParameterException("Aitcraft ID cannot be empty.", 103, 406);
        }
        $statusSystem = GetSystemStatusService::execute();
        if ($statusSystem != SystemStatus::ONLINE) {
            throw new NotAllowedException("The system is not online. It is not possible to add a flight to queue.", 105, 425);
        }

        if(!in_array($flightType, [FlightType::VIP, FlightType::EMERGENCY, FlightType::CARGO, FlightType::PASSENGER])) {
            throw new InvalidParameterException(
                sprintf("The informed flight type %s is not valid. Please, check the documentation.", $flightType), 
                111,
                406
            );
        }

        $aircraft = Aircraft::find($aircraftId);
        if(is_null($aircraft)) {
            throw new InvalidParameterException(sprintf("Aircraft ID %s was not found.", $aircraftId), 106, 404);
        }

        if(count($aircraft->flights()) > 0) {
            throw new NotAllowedException(
                sprintf("Aircraft ID %s is already on the queue. It is not possible to add twice.", $aircraftId), 
                107, 
                409
            );
        }

        $flight = new Flight();
        $flight->aircraftId = $aircraftId;
        $flight->flightType = $flightType;
        $flight->flightNumber = $flightNumber ?? AutoGenerateHelper::generateFlight();
        $flight->save();
    }
}
