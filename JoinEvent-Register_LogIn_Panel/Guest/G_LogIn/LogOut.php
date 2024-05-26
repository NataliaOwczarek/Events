<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../config/config.php";
require_once "../../functions/db_fun.php";

$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];

    session_unset();
    session_destroy();

    $result = addLog($userId, 'Log out', $connection);

    if ($result) {
        http_response_code(200);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding log']);
        http_response_code(500); 
    }

    header("Location: ../../index.php");
}

$connection->close();
?>
