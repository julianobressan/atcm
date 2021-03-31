<?php
namespace ATCM\Core\Interfaces;

/**
 * Provides an interface to Services
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
interface IService
{
        
    /**
     * Execute the function of the service is implemented for
     *
     * @return mixed
     */
    static function execute();
}