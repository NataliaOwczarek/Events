<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../../config/config.php";

$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

function getMaxUserId($connection) {
    $sql = "SELECT MAX(UserId) AS MaxUserId FROM Events_Users";
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["MaxUserId"];
    } else {
        return 0; 
    }
}

function getFavouriteEventsbyUser($userId, $connection) {
    $sql = "SELECT EventId FROM Events_FavouriteEvents WHERE UserId = $userId";
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        $events = array();
        while ($row = $result->fetch_assoc()) {
            $events[] = $row["EventId"];
        }
        return $events;
    } else {
        return array();
    }
}

function getSignedUpEventsByUser($userId, $connection)
{
    $signedUpEvents = array();

    $sql = "SELECT EventId FROM Events_EventMembers WHERE UserId = $userId";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $signedUpEvents[] = $row['EventId'];
        }
    }

    return $signedUpEvents;
}

function removeUserFromEvent($userId, $eventId, $connection)
{
    $sql = "DELETE FROM Events_EventMembers WHERE UserId = $userId AND EventId = $eventId";
    $connection->query($sql);
}

function signUpUserForEvent($userId, $eventId, $connection)
{
    $sql = "INSERT INTO Events_EventMembers (UserId, EventId) VALUES ($userId, $eventId)";
    $connection->query($sql);
}

function addLog($userId, $logType, $connection)
{
    $userId = mysqli_real_escape_string($connection, $userId);
    $logType = mysqli_real_escape_string($connection, $logType);

    if ($logType=='Log In'){
    $sql = "INSERT INTO Events_Logs (UserId) VALUES ('$userId')";
    } else{
    $current_time = date('Y-m-d H:i:s');
    $sql = "Update Events_Logs set LogOutDateTime = '$current_time' where UserId='$userId' and LogOutDateTime is null";    
    }
    $connection->query($sql);

    if ($connection->errno) {
        error_log("Error adding log: " . $connection->error);
        return false;
    } else {
        return true;
    }
}


?>
