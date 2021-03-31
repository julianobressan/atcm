<?php
echo PHP_EOL . "Welcome to installation of Juliano Bressan's Air Traffic Control Manager" . PHP_EOL;
echo "Check it out in https://github.com/julianobressan/atcm" . PHP_EOL. PHP_EOL;
login:
echo "Type a login name for administrator or let empty to use 'admin': " . PHP_EOL;

$login = strtolower(trim(fgets(STDIN)));
system('stty echo');
if(empty($login)) {
    $login = 'admin';
    echo "Login name assumed as 'admin'". PHP_EOL;
} else if(!ctype_alpha($login)) {
    echo "\033[31mOnly letters are allowed.\033[0m". PHP_EOL;
    goto login;
}
password:
if(empty($login)) {
    $login = 'admin';
    echo "Login name assumed as 'admin'". PHP_EOL;
}
echo "Type a password (minimum 6 characters): " . PHP_EOL;
system('stty -echo');
$password = trim(fgets(STDIN));
system('stty echo');
if(strlen($password) < 6) {   
    echo "\033[31mPassword too short.\033[0m". PHP_EOL;
    goto password;
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
    exit("\033[31mIncorrect environment variables.\033[0m" . PHP_EOL);
}

$stmt = $pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbName}'");
$dbExists = (bool) $stmt->fetchColumn();

if($dbExists) {
    exit("\033[31mDatabase already exists. Exiting.\033[0m" . PHP_EOL);
}

$sqlPath = __DIR__ . '/database.sql';
$sql = file_get_contents($sqlPath);
$md5 = md5_file($sqlPath);

$passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
if ($passwordHash === false) {
    exit("\033[31mPassword hash failed. Try again.\033[0m" . PHP_EOL);
}

if($md5 !== '8440516de359f30aac6f143822982738') {
    exit("\033[31mSQL file was modified. Get the original database.sql file and try again.\033[0m" . PHP_EOL);
}

$insertUser = "INSERT INTO `atcm`.`user` (`name`, `password`, `login`) VALUES ('Administrator', '{$passwordHash}', '{$login}');";
//$sql .= $insertUser;
$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 0);
$pdo->exec($sql);
echo "\033[32mDatabase `actm` created\033[0m". PHP_EOL;

$pdo->exec($insertUser);
echo "\033[32mAdministrator user ({$login}) created\033[0m". PHP_EOL;

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

echo "\033[32mThe system was sucessfully installed.\033[0m" . PHP_EOL;
exit();
