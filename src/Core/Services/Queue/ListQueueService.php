<?php

namespace ATCM\Core\Services\Queue;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Enums\AircraftType;
use ATCM\Data\Enums\FlightType;
use ATCM\Data\Models\Queue;
use Exception;

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

        foreach($queue as $flight) {
            $aircraft = $flight->aircraft();
            switch($flight->flightType) {
                case FlightType::EMERGENCY: {
                    $emergencyList[] = [
                        'flight' => $flight,
                        'aircraft' => $aircraft
                    ];
                    break;
                }
                case FlightType::VIP: {
                    $vipList[] = [
                        'flight' => $flight,
                        'aircraft' => $aircraft
                    ];
                    break;
                }
                case FlightType::PASSENGER: {
                    $passengerList[] = [
                        'flight' => $flight,
                        'aircraft' => $aircraft
                    ];
                    break;
                }
                case FlightType::CARGO: {
                    $cargoList[] = [
                        'flight' => $flight,
                        'aircraft' => $aircraft
                    ];
                    break;
                }
                default: {
                    throw new InvalidParameterException(sprintf("Invalid aircraft type: %s", $aircraft->type));
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

    private static function sortAircraftsOfSameType($flights)
    {
        usort($flights, function($ac1, $ac2) {
            if($ac1['aircraft']->size === $ac2['aircraft']->size) {
                return $ac1['aircraft']->createdAt <=> $ac2['aircraft']->createdAt;
            }             
            if($ac1['aircraft']->size === AircraftSize::SMALL) {
                return 1;
            } else {
                return -1;
            }
        });
        return $flights;
    }

}
