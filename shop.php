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
$username = $_SESSION['username'];

$MySQL_DB_HOST = $_ENV['MySQL_DB_HOST'];
$MySQL_DB_USER_NAME = $_ENV['MySQL_DB_USER_NAME'];
$MySQL_DB_PASSWORD = $_ENV['MySQL_DB_PASSWORD'];
$MySQL_DB_NAME = $_ENV['MySQL_DB_NAME'];

$conn = new mysqli($MySQL_DB_HOST, $MySQL_DB_USER_NAME, $MySQL_DB_PASSWORD, $MySQL_DB_NAME);

// Check connection
if ($conn->connect_error) {
  die("Error: " . $conn->connect_error);
}

function queryServer($uuid) {
    $client = new GuzzleHttp\Client();

    $response = $client->get("https://" . $GLOBALS["panel_domain"] . "/api/application/servers", [
        'headers' => [
            'Authorization' => 'Bearer ' . $GLOBALS["ptla_key"],
            'Accept' => 'Application/vnd.pterodactyl.v1+json'
        ],
        'query' => [
            // 'include' => 'user,node',
            'per_page' => 25,
            'filter' => ['uuid' => $uuid]
        ]
    ]);

    $data = json_decode($response->getBody(), true);
    return $data;
}

function buyServerForm() {
    echo "<h1>Buy server plz</h1>";

    die();
}



// get the users id in pterodactyl
$result = $conn->execute_query("SELECT id FROM users WHERE username=?", [$username]);

// Process the result set
if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
        $userid = $row["id"];
    }
}

$result = $conn->execute_query("SELECT uuid FROM servers WHERE owner_id=?", [$userid]);

// Process the result set
if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
        $servers[] = $row["uuid"];
    }
} else {
    echo "no servers";
    buyServerForm();
}

foreach ($servers as $uuid) {
    $serverData[] = queryServer($uuid);
}

print_r($serverData);

?>