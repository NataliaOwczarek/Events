<?php

function validateDateOfBirth($dateOfBirth) {
    $currentDateminus13 = new DateTime('-13 years');
    $minDate = new DateTime('-100 years');
    $inputDate = DateTime::createFromFormat('Y-m-d', $dateOfBirth); 

    return $inputDate <= $currentDateminus13 && $inputDate >= $minDate;
}

function validateEmailFormat($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isEmailUnique($email, $connection) {
    $sql = "SELECT COUNT(*) as count FROM Events_Accounts WHERE Email = '$email' AND IsActive=1";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    return $row['count'] == 0;
}

function isLoginUnique($login, $connection) {
    $sql = "SELECT COUNT(*) as count FROM Events_Accounts WHERE Login = '$login' AND IsActive=1";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    return $row['count'] == 0;
}

function validatePassword($password) {
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,}$/";
    return preg_match($pattern, $password);
}

function passwordsMatch($password, $repeatPassword) {
    return $password === $repeatPassword;
}

function isUserExist($loginOrEmail, $connection) {
    $sql = "SELECT COUNT(*) as count FROM Events_Accounts WHERE Login = '$loginOrEmail' OR Email = '$loginOrEmail' AND IsActive=1";
    $result = $connection->query($sql);
    $row = $result->fetch_assoc();

    return $row['count'] > 0;
}

function isPasswordCorrect($loginOrEmail, $password, $connection) {
    $sql = "SELECT * FROM Events_Accounts WHERE Login = '$loginOrEmail' OR Email = '$loginOrEmail' AND IsActive=1";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['Password'] === $password;
    }

    return false;
}
?>
