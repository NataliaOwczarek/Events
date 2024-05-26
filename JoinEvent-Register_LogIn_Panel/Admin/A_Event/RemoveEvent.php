<?php
session_start();

require_once "../../config/config.php";
require_once "../../functions/db_fun.php";

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['eventId'])) {
        $eventId = intval($_GET['eventId']);

        if (removeEvent($eventId, $connection)) {
            echo 'removed';
        } else {            
            echo 'error';
        }
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}


function removeEvent($eventId, $connection) {
    
    $sql = "DELETE FROM Events_ListOfEvents WHERE EventId = $eventId";        
    $connection->query($sql);

    return true; 
}
?>
