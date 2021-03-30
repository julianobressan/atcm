<?php

namespace ATCM\Core\Services\Queue;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Data\Enums\SystemStatus;
use ATCM\Data\Models\Queue;

/**
 * Add an aircraft to queue
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class DequeueService
{
    public static function execute(int $flightId)
    {
        $statusSystem = GetSystemStatusService::execute();
        if ($statusSystem != SystemStatus::ONLINE) {
            throw new NotAllowedException("The system is not online. It is not possible to add an aircraft.", 102, 425);
        }

        $flightsQueue = ListQueueService::execute();
        if(count($flightsQueue) === 0) {
            throw new NotAllowedException("There is no flight on queue. It is not possible to dequeue.", 103, 406);
        }
        $flight = $flightsQueue[0]['flight'];
        if($flight->id == $flightId) {
            $flight->delete();
        } else {
            $informedFlight = Queue::find($flightId, true);
            if(is_null($informedFlight)) {
                throw new InvalidParameterException("The informed flight does not exists.", 101, 404);
            } else if(!is_null($informedFlight->deletedAt)) {
                throw new NotAllowedException(
                    "The informed flight is no longer in the queue. Probably was dequeued by other user.", 
                    101, 
                    409
                );
            } else {
                throw new NotAllowedException(
                    "The informed flight does not have the priority to dequeue.", 
                    101, 
                    409
                );
            }
        }
        
    }
}
