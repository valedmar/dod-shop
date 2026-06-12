<?php
require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$domain = $_ENV["DOMAIN"];

session_start();
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: https://$domain/index.php");
    die();

}

$username = $_SESSION['username'];

echo "Hello $username, the shop will open soon.";
?>