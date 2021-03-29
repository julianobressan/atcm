<?php

namespace ATCM\Core\Services\Queue;

use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Data\Enums\SystemStatus;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Queue;
use Exception;
use InvalidArgumentException;
use LogicException;

/**
 * Add an aircraft to queue
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class AddAircraftToQueueService
{
    public static function execute($aircraftId)
    {
        $statusSystem = GetSystemStatusService::execute();
        if ($statusSystem != SystemStatus::ONLINE) {
            throw new NotAllowedException("The system is not online. It is not possible to add an aircraft.");
        }

        $aircraft = Aircraft::find($aircraftId);
        if(is_null($aircraft)) {
            throw new InvalidArgumentException("Aircraft ID %s was not found.", $aircraftId);
        }

        if(count($aircraft->enqueued()) > 0) {
            throw new NotAllowedException(sprintf("Aircraft ID %s is already on the queue. It is not possible to add twice.", $aircraftId));
        }

        $queue = new Queue();
        $queue->aircraftId = $aircraftId;
        $queue->save();
    }
}
