<?php
session_start();
require_once "../../config/config.php";
require_once "../../functions/db_fun.php";

$userId = $_SESSION['user_id'];
$eventId = $_GET['eventId'];

$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

if (in_array($eventId, array_values(getSignedUpEventsByUser($userId, $connection)))) {
    removeUserFromEvent($userId, $eventId, $connection);
    echo 'removed';
} else {
    signUpUserForEvent($userId, $eventId, $connection);
    echo 'added';
}

mysqli_close($connection);
?>
