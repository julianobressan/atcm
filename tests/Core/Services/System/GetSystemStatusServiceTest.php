<?php

use ATCM\Core\Services\System\GetSystemStatusService;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class GetSystemStatusServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../../");
        $dotenv->load();
    }

    public function testExecute()
    {
        $return = GetSystemStatusService::execute();
        assertEquals('online', $return);
    }
}