<?php
require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$domain = $_ENV["DOMAIN"];

if(isset($_GET['logout-submit']) && $_GET['logout-submit'] == 'logout') {
    session_start();

    // Unset all of the session variables.
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["username"]
        );
    }

    // Finally, destroy the session.
    session_destroy();
}

header("Location: http://$domain/index.php");
die();

?>