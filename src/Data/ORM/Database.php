<?php

namespace ATCM\Data\ORM;

use ATCM\Core\Exceptions\DataAccessException;

final class Database
{
    private static $connection;

    /**
     * Singleton: Método construtor privado para impedir classe de gerar instâncias
     *
     */
    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    private function __wakeup()
    {
        
    }

    /**
     * Método montar string de conexao e gerar o objeto PDO
     * @param $dados array
     * @return PDO
     */
    private static function make(): \PDO
    {               
        $dbType = $_ENV['DB_TYPE'];
        $dbHost = $_ENV['DB_HOST'];
        $dbPort = $_ENV['DB_PORT'];
        $dbName = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASS'];

        if (!is_null($dbType) && !is_null($dbUser) && !is_null($dbPass) && !is_null($dbName) && !is_null($dbHost)) {
            switch (strtoupper($dbType)) {
                case 'MYSQL' : $port = $dbPort ?? 3306;
                    return new \PDO("mysql:host={$dbHost};port={$port};dbname={$dbName}", $dbUser, $dbPass);
                    break;
                case 'MSSQL' : $porta = isset($porta) ? $porta : 1433;
                    return new \PDO("mssql:host={$dbHost},{$porta};dbname={$dbName}", $dbUser, $dbPass);
                    break;
                case 'PGSQL' : $porta = isset($porta) ? $porta : 5432;
                    return new \PDO("pgsql:dbname={$dbName}; user={$dbUser}; password={$dbPass}, host={$dbHost};port={$porta}");
                    break;
                case 'SQLITE' : return new \PDO("sqlite:{$dbName}");
                    break;
                case 'OCI8' : return new \PDO("oci:dbname={$dbName}", $dbUser, $dbPass);
                    break;
                case 'FIREBIRD' : return new \PDO("firebird:dbname={$dbName}", $dbUser, $dbPass);
                    break;
            }
        } else {
            throw new DataAccessException('Database connection data is missing');
        }
    }

    /**
     * Método estático que devolve a instancia ativa
     *
     */
    public static function getInstance(): \PDO
    {
        if (self::$connection == NULL) {
            self::$connection = self::make();
            self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$connection->exec("set names utf8");
        }
        return self::$connection;
    } 
}