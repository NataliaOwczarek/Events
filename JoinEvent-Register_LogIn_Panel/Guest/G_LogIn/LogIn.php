<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../config/config.php";
require_once "../../functions/db_fun.php";
require_once "../../functions/validation_fun.php";

$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginOrEmail = $_POST["email"];
    $password = $_POST["password"];

    if (!isUserExist($loginOrEmail, $connection)) {
        echo '<script>alert("Nieprawidłowy login lub hasło.");</script>';
        echo '<script>window.location.href = "GuestLogIn.php";</script>';
    } elseif (!isPasswordCorrect($loginOrEmail, $password, $connection)) {
        echo '<script>alert("Nieprawidłowy login lub hasło.");</script>';
        echo '<script>window.location.href = "GuestLogIn.php";</script>';
    } else {
        $loginOrEmail = mysqli_real_escape_string($connection, $loginOrEmail);
        $sql = "SELECT Login, IsAdmin, UserId FROM Events_Accounts WHERE (Login = '$loginOrEmail' OR Email = '$loginOrEmail') AND IsActive=1";
        $result = $connection->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['user_login'] = $row['Login'];
            $_SESSION['user_id'] = $row['UserId'];
            addLog($row['UserId'], 'Log In', $connection);

            if ($row['IsAdmin'] == 1) {
                header("Location: ../../Admin/A_Home/AdminHome.php");
            } else {
                header("Location: ../../User/U_Home/UserHome.php");
            }
        } else {
            echo "Błąd podczas pobierania informacji o użytkowniku.";
        }
    }
}

$connection->close();
?>
