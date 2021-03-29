<?php

namespace ATCM\Core\Services\Queue;

use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Data\Enums\SystemStatus;
use ATCM\Data\Models\Aircraft;
use ATCM\Data\Models\Queue;
use Exception;
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
            throw new LogicException("The system is not online. It is not possible to add an aircraft.");
        }

        //$aircraftOnQueue = Que
    }
}
