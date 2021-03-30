<?php
echo "Type a login name for administrator or let empty to use 'admin': " . PHP_EOL;
$login = trim(fgets(STDIN));
if(empty($login)) {
    $login = 'admin';
    echo "Login name assumed as 'admin'". PHP_EOL;
} 
echo "Type a password (minimum 6 characters): " . PHP_EOL;
$password = trim(fgets(STDIN));
if(strlen($password) < 6) {   
    exit("Password too short.". PHP_EOL);
}

require __DIR__ . '/vendor/autoload.php';

$environmentVars = \Dotenv\Dotenv::createImmutable(__DIR__);
$environmentVars->load();

$dbType = $_ENV['DB_TYPE'];
$dbHost = $_ENV['DB_HOST'];
$dbPort = $_ENV['DB_PORT'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
$pdo = null;

if (!is_null($dbType) && !is_null($dbUser) && !is_null($dbPass) && !is_null($dbName) && !is_null($dbHost)) {
     $pdo =  new \PDO("mysql:host={$dbHost};port={$dbPort}", $dbUser, $dbPass);
} else {
    exit("Incorrect environment variables.". PHP_EOL);
}

$stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbName}'");
$dbExists = (bool) $stmt->fetchColumn();

if($dbExists) {
    exit("Database already exists.".PHP_EOL);
}

$sqlPath = __DIR__ . '/database.sql';
$sql = file_get_contents($sqlPath);
$md5 = md5_file($sqlPath);

$passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
if ($passwordHash === false) {
    throw new \Exception("Password hash failed. Try again.");
    exit(0);
}

if($md5 !== '0a2b691e83aacb5a1b7c390f086d555d') {
    throw new Exception("SQL file was modified. Get the original database.sql file and try again.");
    exit(0);
}
$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 0);
$pdo->exec($sql);

$insertUser = "INSERT INTO `atcm`.`user` (`name`, `password`, `login`) VALUES 'Administrator', '{$passwordHash}', '{$login}');";

echo "The system was sucefully installed. For security reasons, delete the install.php file.".PHP_EOL;
exit();
