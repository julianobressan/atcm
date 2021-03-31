<?php
echo PHP_EOL . "\033[31mWelcome to installation of Juliano Bressan's Air Traffic Control Manager \033[0m" . PHP_EOL;
echo "Check it out in https://github.com/julianobressan/atcm" . PHP_EOL. PHP_EOL;
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
    exit("Incorrect environment variables." . PHP_EOL);
}

$stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbName}'");
$dbExists = (bool) $stmt->fetchColumn();

if($dbExists) {
    exit("Database already exists. Exiting." . PHP_EOL);
}

$sqlPath = __DIR__ . '/database.sql';
$sql = file_get_contents($sqlPath);
$md5 = md5_file($sqlPath);

$passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
if ($passwordHash === false) {
    exit("Password hash failed. Try again." . PHP_EOL);
}

if($md5 !== '0a2b691e83aacb5a1b7c390f086d555d') {
    exit("SQL file was modified. Get the original database.sql file and try again." . PHP_EOL);
}
$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 0);
$pdo->exec($sql);

$insertUser = "INSERT INTO `atcm`.`user` (`name`, `password`, `login`) VALUES 'Administrator', '{$passwordHash}', '{$login}');";

deletefiles:
echo "It is recommended that database.sql and install.php files must be deleted, for security reasons. Do you want do delete now?" . PHP_EOL;
echo "[Y] / N" . PHP_EOL;
$delete = trim(fgets(STDIN));
if(strtoupper($delete) == "Y" || empty($delete)) {
    unlink(__DIR__ . '/database.sql');
    unlink(__FILE__);
} else if(strtoupper($delete) == "N") {
    echo "Skipping deletion of installation files" . PHP_EOL;
} else goto deletefiles;

echo "The system was sucefully installed." . PHP_EOL;
exit();
