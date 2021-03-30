<?php

namespace ATCM\Core\Services\Installation;

use ATCM\Core\Exceptions\InvalidParameterException;
use ATCM\Core\Exceptions\NotAllowedException;
use ATCM\Data\Models\User;
use ATCM\Data\ORM\Database;

/**
 * Add an aircraft to queue
 * 
 * @author Juliano Bressan <bressan.rs@gmail.com>
 * @version 1.0.0
 * @copyright MIT
 */
class InstallService
{
    public static function execute($loginAdmin, $name, $password)
    {
        try {
            $user = User::first();
            throw new NotAllowedException("The system is already installed.");
        }  catch(NotAllowedException $ex) {
            throw $ex;
        } catch (\Throwable $th) {
            //throw $th;
        }        
       
        $dbType = $_ENV['DB_TYPE'];
        $dbHost = $_ENV['DB_HOST'];
        $dbPort = $_ENV['DB_PORT'];
        $dbName = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASS'];
        $pdo = null;
        if (!is_null($dbType) && !is_null($dbUser) && !is_null($dbPass) && !is_null($dbName) && !is_null($dbHost)) {
            $pdo =  new \PDO("mysql:host={$dbHost};port={$dbPort}", $dbUser, $dbPass);
        }
        $sqlPath = __DIR__ . '/../../../../database.sql';
        $sql = file_get_contents($sqlPath);
        $md5 = md5_file($sqlPath);
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        if ($passwordHash === false) {
            throw new \Exception("Password hash failed. Try again.");
        }

        if($md5 !== '0a2b691e83aacb5a1b7c390f086d555d') {
            throw new InvalidParameterException("SQL file was modified. Get the original database.sql file and try again.");
        }
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 0);
        $pdo->exec($sql);

        $user = new User();
        $user->login = $loginAdmin;
        $user->name = $name;
        $user->password = $passwordHash;
        $user->save();
        
    }
}
