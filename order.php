<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$domain = $_ENV["DOMAIN"];
$panel_domain = $_ENV["PANEL_DOMAIN"];
$ptla_key = $_ENV["PTLA_KEY"];

session_start();
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: https://$domain/index.php");
    die();
}


if(isset($_POST['name']) && $_POST['name'] != '') {
    $name = htmlspecialchars($_POST["name"]);
} else {
    header("Location: https://$domain/shop.php");
    die();
}

$MySQL_DB_HOST = $_ENV['MySQL_DB_HOST'];
$MySQL_DB_USER_NAME = $_ENV['MySQL_DB_USER_NAME'];
$MySQL_DB_PASSWORD = $_ENV['MySQL_DB_PASSWORD'];
$MySQL_DB_NAME = $_ENV['MySQL_DB_NAME'];

$conn = new mysqli($MySQL_DB_HOST, $MySQL_DB_USER_NAME, $MySQL_DB_PASSWORD, $MySQL_DB_NAME);

// Check connection
if ($conn->connect_error) {
  die("Error: " . $conn->connect_error);
}

$result = $conn->execute_query("SELECT id FROM allocations WHERE server_id IS NULL LIMIT 1");

if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
        $allocation = $row["id"];
    }
} 

$userid = $_SESSION["id"];
$client = new GuzzleHttp\Client();

$serverData = [
    'name' => $name,
    'user' => $userid,
    'egg' => 1,
    'docker_image' => 'ghcr.io/pterodactyl/yolks:java_25',
    'startup' => 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}',
    'environment' => [
        'VANILLA_VERSION' => 'latest',
        'SERVER_JARFILE' => 'server.jar'
    ],
    'limits' => [
        'memory' => 4096,
        'swap' => 0,
        'disk' => 0,
        'io' => 500,
        'cpu' => 100,
        'oom_disabled' => false
    ],
    'feature_limits' => [
        'databases' => 2,
        'allocations' => 1,
        'backups' => 5
    ],
    'allocation' => [
        'default' => $allocation
    ]
];

// print_r($serverData);

$response = $client->post("https://$panel_domain/api/application/servers", [
    'headers' => [
        'Authorization' => 'Bearer ' . $ptla_key,
        'Accept' => 'Application/vnd.pterodactyl.v1+json',
        'Content-Type' => 'application/json'
    ],
    'json' => $serverData
]);

// $data = json_decode($response->getBody(), true);
// print_r($data);

header("Location: https://$domain/shop.php");
die();
?>
