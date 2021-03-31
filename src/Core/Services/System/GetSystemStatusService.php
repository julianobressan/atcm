<?php

namespace ATCM\Core\Services\System;

use ATCM\Core\Interfaces\IService;
use ATCM\Data\Enums\SystemStatus;

 /**
 * Execute a verification if system is online, returning the status acording SystemStatus enum
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class GetSystemStatusService implements IService
{
    public static function execute(): string
    {
        $pathToFile = __DIR__."/../../../../" . $_ENV['SYSTEM_INFO_FILE'];
        try {
            $content = trim(file_get_contents($pathToFile));
            if($content === "online") return SystemStatus::ONLINE;
            if($content === "booting") return SystemStatus::BOOTING;
            if($content === "halting") return SystemStatus::HALTING;
            return SystemStatus::OFFLINE;
        } catch(\Exception $e) {
            return SystemStatus::OFFLINE;
        }      
       
    }
}
