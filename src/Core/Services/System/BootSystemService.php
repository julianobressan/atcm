<?php

namespace ATCM\Core\Services\System;

 /**
 * Execute a verification if system is online, returning the status acording SystemStatus enum
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class BootSystemService
{
    public static function execute()
    {
        $pathToFile = __DIR__."/../../../../" . $_ENV['SYSTEM_INFO_FILE'];
        try {
            file_put_contents($pathToFile, "online");
        } catch(\Exception $e) {
            throw $e;
        }      
       
    }
}
