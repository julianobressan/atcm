<?php

use ATCM\Core\Services\Installation\InstallService;
use PHPUnit\Framework\TestCase;


class InstallServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../../");
        $dotenv->load();
    }

    public function testExecute()
    {
        InstallService::execute("admin", "Norton Abdulah", '123456');

    }
    

}