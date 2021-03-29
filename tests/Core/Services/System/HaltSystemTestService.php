<?php

use ATCM\Core\Helpers\StringHelper;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Core\Services\System\HaltSystemService;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class HaltSystemTestService extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../../");
        $dotenv->load();
    }

    public function testExecute()
    {
        HaltSystemService::execute();
        $status = GetSystemStatusService::execute();
        assertEquals('offline', $status);
    }
}