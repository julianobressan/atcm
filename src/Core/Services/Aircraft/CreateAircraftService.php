<?php

namespace ATCM\Core\Services\Aircraft;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Core\Helpers\AutoGenerateHelper;
use ATCM\Core\Interfaces\IService;
use ATCM\Data\Enums\AircraftSize;
use ATCM\Data\Models\Aircraft;

/**
 * Execute a verification if system is online, returning the status acording SystemStatus enum
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class CreateAircraftService implements IService
{
    public static function execute(string $size = null, $model = null)
    {      
        if(!in_array($size, [AircraftSize::LARGE, AircraftSize::SMALL])) {
            throw new InvalidParameterException(sprintf("The informed size %s is not valid. Please, check the documentation.", $size), 112, 406);
        }

        $aircraft = new Aircraft();
        $aircraft->size = $size;
        $aircraft->model = $model ?? AutoGenerateHelper::generateModel($size);
        $aircraft->save();

        return $aircraft;
    }
}
