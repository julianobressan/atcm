<?php

namespace ATCM\Core\Services\Queue;

use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Data\Enums\SystemStatus;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Queue;
use InvalidArgumentException;

/**
 * Add an aircraft to queue
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class DequeueService
{
    public static function execute()
    {
        $statusSystem = GetSystemStatusService::execute();
        if ($statusSystem != SystemStatus::ONLINE) {
            throw new NotAllowedException("The system is not online. It is not possible to add an aircraft.");
        }

        $aircraftsQueue = ListQueueService::execute();
        if(count($aircraftsQueue) === 0) {
            throw new NotAllowedException("There is no aircraft on queue. It is not possible to dequeue.");
        }

        $aircraftsQueue[0]->enqueued()[0]->delete();
    }
}
