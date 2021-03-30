<?php

namespace ATCM\Core\Services\Queue;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Helpers\AutoGenerateHelper;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Data\Enums\FlightType;
use ATCM\Data\Enums\SystemStatus;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Queue;

/**
 * Add an aircraft to queue
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class EnqueueAircraftService
{
    public static function execute($aircraftId, $flightType, $flightNumber = null)
    {
        $statusSystem = GetSystemStatusService::execute();
        if ($statusSystem != SystemStatus::ONLINE) {
            throw new NotAllowedException("The system is not online. It is not possible to add an aircraft.", 105, 425);
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

        if(count($aircraft->enqueued()) > 0) {
            throw new NotAllowedException(
                sprintf("Aircraft ID %s is already on the queue. It is not possible to add twice.", $aircraftId), 
                107, 
                409
            );
        }

        $queue = new Queue();
        $queue->aircraftId = $aircraftId;
        $queue->flightType = $flightType;
        $queue->flightNumber = $flightNumber ?? AutoGenerateHelper::generateFlight();
        $queue->save();
    }
}
