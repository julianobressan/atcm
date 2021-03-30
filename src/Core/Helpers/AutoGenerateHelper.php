<?php

namespace ATCM\Core\Helpers;

use ATCM\Data\Enums\AircraftSize;

/**
 * A helper class to auto generate models and flights numbers to aircrafts
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class AutoGenerateHelper
{
    private static $largeModels = [
        'Boeing 747-8',
        'Airbus A380',
        'Boeing 777',
        'Airbus A350',
        'Boeing 787',
        'Antonov An-225 Mriya',
        'Antonov An-124',
        'Embraer KC390',
        'Lockheed C130',
        'IIyushin II-76'
    ];
    
    private static $smallModels = [
        'De Havilland Canada DHC-G Twin Otter',
        'Bombardier CRJ',
        'Embraer 160',
        'Embraer E-Jet',
        'Cessna Citation II',
        'Cessna Grand Caravan',
        'Piper PA-28R-180 Arrow',
        'Piper T Saratoga SP',
        'Beechcraft King Air 350'
    ];

    public static function generateModel($size) {
        if($size === AircraftSize::LARGE) {
            $index = random_int(0, count(self::$largeModels) - 1);
            return self::$largeModels[$index];
        } else {
            $index = random_int(0, count(self::$smallModels) - 1);
            return self::$smallModels[$index];
        }
    }

    public static function generateFlight() {
        $letters = chr(rand(65, 90)) . chr(rand(65, 90));
        $number = random_int(101, 9999);

        return $letters . " " . $number;
    }
}