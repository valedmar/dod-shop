<?php
require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "hello world", "<br>\n";


$appName = $_ENV['APP_NAME'];

echo $appName;

?>