<?php
namespace ATCM\Core\Interfaces;

/**
 * Provides an interface to REST API Exceptions
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
interface IRestAPIException
{
    function getHttpCode(): int;

    function __toString();
}