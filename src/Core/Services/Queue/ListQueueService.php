<?php

namespace ATCM\Core\Services\Queue;

use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
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
class ListQueueService
{
    public static function execute()
    {
        $queue = Queue::all();
        $emergencyList = [];
        $vipList = [];
        $passengerList = [];
        $cargoList = [];

        foreach($queue as $item) {
            $aircraft = $item->aircraft();
            switch($aircraft->type) {
                case AircraftType::EMERGENCY: {
                    $emergencyList[] = $aircraft;
                    break;
                }
                case AircraftType::VIP: {
                    $vipList[] = $aircraft;
                    break;
                }
                case AircraftType::PASSENGER: {
                    $passengerList[] = $aircraft;
                    break;
                }
                case AircraftType::CARGO: {
                    $cargoList[] = $aircraft;
                    break;
                }
            }
        }

        $emergencyListSorted = self::sortAircraftsOfSameType($emergencyList);
        $vipListSorted = self::sortAircraftsOfSameType($vipList);
        $passengerListSorted = self::sortAircraftsOfSameType($passengerList);
        $cargoListSorted = self::sortAircraftsOfSameType($cargoList);

        $finalList = [...$emergencyListSorted, ...$vipListSorted, ...$passengerListSorted, ...$cargoListSorted];
        return $finalList;
    }

    private static function sortAircraftsOfSameType($aircrafts)
    {
        usort($aircrafts, function($ac1, $ac2) {
            if($ac1->size === $ac2->size) {
                return $ac1->createdAt <=> $ac2->createdAt;
            }             
            if($ac1->size === AircraftSize::SMALL) {
                return 1;
            } else {
                return -1;
            }
        });
        return $aircrafts;
    }

}
