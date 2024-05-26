<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../../config/config.php";
require_once "../../functions/db_fun.php";
require_once "../../functions/other_fun.php";
require_once "../../functions/validation_fun.php";

$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imie = $_POST["name"];
    $nazwisko = $_POST["surname"];
    $data_urodzenia = $_POST["date_of_birth"];
    $plec = $_POST["gender"];
    $email = $_POST["email"];
    $login = $_POST["login"];
    $haslo = $_POST["password"];
    $powtorz_haslo = $_POST["repeat_password"];

    try {
        if (!validateDateOfBirth($data_urodzenia)) {
            throw new Exception("Nieprawidłowa data urodzenia.");
        }

        if (!validateEmailFormat($email)) {
            throw new Exception("Nieprawidłowy adres email.");
        }

        if (!validatePassword($haslo)) {
            throw new Exception("Hasło musi zawierać min. 8 znaków:
            * co najmniej jedna wielka litera
            * co najmniej jedna cyfra
            * co najmniej jeden znak");
        }

        if (!passwordsMatch($haslo, $powtorz_haslo)) {
            throw new Exception("Podane hasła różnią się od siebie.");
        }

        if (!isEmailUnique($email, $connection)) {
            throw new Exception("W bazie istnieje użytkownik o podanym adresie email.");
        }

        if (!isLoginUnique($login, $connection)) {
            throw new Exception("Login jest już zajęty.");
        }

        $data_urodzenia = date("Y-m-d", strtotime($data_urodzenia));

        $sqlCheckUsers = "SELECT COUNT(*) as userCount FROM Events_Users";
        $result = $connection->query($sqlCheckUsers);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userCount = $row["userCount"];
            echo $userCount;

            $isAdmin = $userCount == 0 ? 1 : 0;
        }

        echo "Is Admin: " . $isAdmin;

        $sqlPersonalData = "INSERT INTO Events_Users (Name, Surname, Sex, Date_of_birth) VALUES ('$imie', '$nazwisko', '$plec', '$data_urodzenia')";

        if ($connection->query($sqlPersonalData) === TRUE) {
            echo "Dane osobowe zostały dodane pomyślnie.";

            $userId = getMaxUserId($connection);
            $activationCode = generateActivationCode(16, $connection);
            echo $activationCode;

            $sqlUserAccount = "INSERT INTO Events_Accounts (UserId, IsAdmin, Email, Login, Password, VerifyCode, IsActive) VALUES ('$userId', '$isAdmin', '$email', '$login', '$haslo', '$activationCode', 1)";
            $activationLink = generateActivationLink($userId, $connection, $activationCode);
            echo $activationLink;
            sendActivationEmail($email, $user, $activationLink);

            if ($connection->query($sqlUserAccount) === TRUE) {
                echo "Dane konta użytkownika zostały dodane pomyślnie.";
                header("Location: ../G_LogIn/GuestLogIn.php");
                exit();
            } else {
                throw new Exception("Błąd podczas dodawania danych konta użytkownika: " . $connection->error);
            }
        } else {
            throw new Exception("Błąd podczas dodawania danych osobowych: " . $connection->error);
        }
    } catch (Exception $e) {
        echo "Wystąpił błąd: " . $e->getMessage();
    }
}

$connection->close();
?>
