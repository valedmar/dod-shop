<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$MySQL_DB_HOST = $_ENV['MySQL_DB_HOST'];
$MySQL_DB_USER_NAME = $_ENV['MySQL_DB_USER_NAME'];
$MySQL_DB_PASSWORD = $_ENV['MySQL_DB_PASSWORD'];
$MySQL_DB_NAME = $_ENV['MySQL_DB_NAME'];

$conn = new mysqli($MySQL_DB_HOST, $MySQL_DB_USER_NAME, $MySQL_DB_PASSWORD, $MySQL_DB_NAME);

// Check connection
if ($conn->connect_error) {
  die("Error: " . $conn->connect_error);
}

$username = htmlspecialchars($_POST["username"]);
$userpassword = htmlspecialchars($_POST["password"]);

$result = $conn->execute_query("SELECT password FROM users WHERE username=?", [$username]);

// Process the result set
if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
    $passwordhash = $row["password"];
    if (password_verify($userpassword, $passwordhash)) {
      // echo "logged in";
      session_start();
      $_SESSION['username'] = $username;
      header("Location: http://shop.slyshaft.com/shop.php");
      die();
    } else {
      header("Location: http://shop.slyshaft.com/index.php?error=pwd");
      die();
    }
  }
} else {
  header("Location: http://shop.slyshaft.com/index.php?error=usr");
  die();
}


?>