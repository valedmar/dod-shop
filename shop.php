<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$appName = $_ENV["APP_NAME"];
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

?>
    <html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title><?php echo $appName; ?> | Servers</title>
        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
        </style>
    </head>
<?php

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

function displayServers($servers) {
    foreach ($servers as $uuid) {
        $serverData[] = queryServer($uuid);
    }

    // print_r($serverData);

    ?>
    <table style="width:60%">
    <caption style='font-size:25px;'>Your servers</caption>
    <tr>
        <th>Server name</th>
        <th>Server game</th>
        <th>Server memory</th>
        <th>Server disk</th>
    </tr>
    <?php
    foreach ($serverData as $server) {
        $server = $server["data"][0]["attributes"];
        if ($server["nest"] == 1) {
            $server["nest"] = "Minecraft";
        }
        if ($server["limits"]["disk"] == 0) {
            $server["limits"]["disk"] = "Unlimited";
        }
        print "
        <tr>
            <td>" . $server["name"] . "</td>
            <td>" . $server["nest"] . "</td>
            <td>" . $server["limits"]["memory"] . " MiB</td>
            <td>" . $server["limits"]["disk"] . " GiB</td>
        </tr>
        ";
    }
    ?>
    </table>
    </hmtl>
<?php
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

$_SESSION["id"] = $userid;

$result = $conn->execute_query("SELECT uuid FROM servers WHERE owner_id=?", [$userid]);

// Process the result set
if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
        $servers[] = $row["uuid"];
    }
  displayServers($servers);
} else {
    echo "<h2>No servers</h2>";
}

?>
<br>
<form action="order.php" method="post">
    <label for="name">New server name:</label>
    <input name="name" id="name" type="text">
    <button type="submit">Order</button>
</form>
<br>
 <!-- let's make a signout button yher -->
<li style='font-size:20px;'><a href='https://<?php echo $panel_domain ?>'>Panel</a></li>
<li style='font-size:20px;'><a href='./logout.php?logout-submit=logout'>Logout</a></li>