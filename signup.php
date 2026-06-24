<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$domain = $_ENV["DOMAIN"];
$panel_domain = $_ENV["PANEL_DOMAIN"];

$MySQL_DB_HOST = $_ENV['MySQL_DB_HOST'];
$MySQL_DB_USER_NAME = $_ENV['MySQL_DB_USER_NAME'];
$MySQL_DB_PASSWORD = $_ENV['MySQL_DB_PASSWORD'];
$MySQL_DB_NAME = $_ENV['MySQL_DB_NAME'];

$conn = new mysqli($MySQL_DB_HOST, $MySQL_DB_USER_NAME, $MySQL_DB_PASSWORD, $MySQL_DB_NAME);

// Check connection
if ($conn->connect_error) {
  die("Error: " . $conn->connect_error);
}

if (!isset($_POST["email"]) || empty($_POST["email"])) {
    header("Location: https://$domain/index.php");
    die();
} else {
  $useremail = htmlspecialchars($_POST["email"]);
}

if (!isset($_POST["username"]) || empty($_POST["username"])) {
    header("Location: https://$domain/index.php");
    die();
} else {
  $username = htmlspecialchars($_POST["username"]);
}

if (!isset($_POST["password"]) || empty($_POST["password"])) {
    header("Location: https://$domain/index.php");
    die();
} else {
  $userpassword = htmlspecialchars($_POST["password"]);
}

// check if mail is taken
$result = $conn->execute_query("SELECT username FROM users WHERE email=?", [$useremail]);

// Process the result set
if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
        header("Location: http://$domain/index.php?error=mail&view=r");
        die();
    }
}

//check if username is taken
$result = $conn->execute_query("SELECT username FROM users WHERE username=?", [$username]);
// Process the result set
if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
    header("Location: http://$domain/index.php?error=taken&view=r");
    die();
  }
} else { // create the user
    $ptla_key = $_ENV["PTLA_KEY"];
    $client = new GuzzleHttp\Client();

    $userData = [
        'email' => $useremail,
        'username' => $username,
        'first_name' => 'New',
        'last_name' => 'User',
        'password' => $userpassword,
        'language' => 'en',
        'root_admin' => false
    ];

    $response = $client->post("https://$panel_domain/api/application/users", [
        'headers' => [
            'Authorization' => 'Bearer '. $ptla_key,
            'Accept' => 'Application/vnd.pterodactyl.v1+json',
            'Content-Type' => 'application/json'
        ],
        'json' => $userData
    ]);

    // $data = json_decode($response->getBody(), true);
    //print_r($data);

    header("Location: http://$domain/index.php?error=create");
    die();
}


?>