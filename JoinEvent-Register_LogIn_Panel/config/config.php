<?php
    $host = 'localhost'; 
    $user = '2024_basia26'; 
    $password = '387253';
    $dbname = '2024_basia26';
    $prefix = 'Events_';   

    $link = mysqli_connect($host, $user, $password, $dbname);

    if ($link === false) {
        die("Connection failed: " . mysqli_connect_error());
    }

    mysqli_select_db($link, $dbname) or die("Nie można wybrać bazy danych");
    mysqli_query($link, "SET NAMES UTF8");
?>