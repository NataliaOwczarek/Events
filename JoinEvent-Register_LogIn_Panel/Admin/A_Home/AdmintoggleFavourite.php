<?php
session_start();
require_once "../../config/config.php";
require_once "../../functions/db_fun.php";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["eventId"])) {
    $eventId = $_GET["eventId"];
    $userId = $_SESSION['user_id'];
    $connection = mysqli_connect($host, $user, $password, $dbname);

    if ($connection->connect_error) {
        die("Błąd połączenia: " . $connection->connect_error);
    }

    if (isEventInFavorites($eventId, $userId, $connection)) {
        removeEventFromFavorites($eventId, $userId, $connection);
        echo "removed";
    } else {
        addEventToFavorites($eventId, $userId, $connection);
        echo "added";
    }

    mysqli_close($connection);
}

function isEventInFavorites($eventId, $userId, $connection)
{
    $sql = "SELECT EventId FROM Events_FavouriteEvents WHERE UserId = $userId AND EventId = $eventId";
    $result = $connection->query($sql);

    return ($result && $result->num_rows > 0);
}

function addEventToFavorites($eventId, $userId, $connection)
{
    $sql = "INSERT INTO Events_FavouriteEvents (UserId, EventId) VALUES ($userId, $eventId)";
    $connection->query($sql);
}

function removeEventFromFavorites($eventId, $userId, $connection)
{
    $sql = "DELETE FROM Events_FavouriteEvents WHERE UserId = $userId AND EventId = $eventId";
    $connection->query($sql);
}
?>
