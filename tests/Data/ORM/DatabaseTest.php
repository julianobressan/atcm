<?php

use ATCM\Data\ORM\Database;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class DatabaseTest extends TestCase
{
    protected function setUp(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/../../../");
        $dotenv->load();
    }

    public function testCreateDatabase()
    {
        $database = Database::getInstance();
        assertEquals(\PDO::class, get_class($database));
    }

    
}