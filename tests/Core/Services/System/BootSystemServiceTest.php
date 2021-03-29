<?php

use ATCM\Core\Helpers\StringHelper;
use ATCM\Core\Services\System\GetSystemStatusService;
use ATCM\Core\Services\System\BootSystemService;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class BootSystemServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../../");
        $dotenv->load();
    }

    public function testExecute()
    {
        BootSystemService::execute();
        $status = GetSystemStatusService::execute();
        assertEquals('online', $status);
    }
}