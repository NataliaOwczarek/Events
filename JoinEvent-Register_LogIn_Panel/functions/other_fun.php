<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../../config/config.php";

$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

function generateActivationCode($length = 16, $connection) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $maxAttempts = 10;
    $attempt = 0;

    do {
        $activationCode = '';
        for ($i = 0; $i < $length; $i++) {
            $activationCode .= $characters[rand(0, $charactersLength - 1)];
        }
        $sqlCheckCode = "SELECT COUNT(*) as codeCount FROM Events_Accounts WHERE VerifyCode = '$activationCode'";
        $result = $connection->query($sqlCheckCode);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $codeCount = $row['codeCount'];
        } else {
            $codeCount = 0;
        }

        $attempt++;
    } while ($codeCount > 0 && $attempt < $maxAttempts);

    if ($codeCount === 0) {
        $sqlInsertCode = "INSERT INTO Events_Accounts (VerifyCode) VALUES ('$activationCode')";
        $connection->query($sqlInsertCode);
        return $activationCode;
    } else {
        return false;
    }
}

function generateActivationLink($userId, $connection, $activationCode) {

    $sql = "Select (UserId, VerifyCode) from Events_Accounts where UserId='$userId'";
    $result = $connection->query($sql);

    if ($result) {
        $baseUrl = 'https://http://212.191.66.19/~basia26/Events/Guest/G_Register/ActivateAccount.php';
        $activationLink = $baseUrl . '?user=' . $userId . '&code=' . $activationCode;

        return $activationLink;
    } else {
        return false;
    }
}



function sendActivationEmail($email, $username, $activationLink) {
    $message = "
        <html>
        <head>
            <!-- Style CSS -->
        </head>
        <body>
            <div id='header'>
                <img id='logo' src='https://example.com/logo.png' alt='Logo'>
            </div>
            <div id='content'>
                <p>Witaj $username,</p>
                <p>Aby aktywować konto użytkownika na stronie D&B&N Events, kliknij w poniższy link:</p>
                <p><a href='$activationLink'>$activationLink</a></p>
                <p>Jeśli to nie Ty, zignoruj tę wiadomość.</p>
                <p>Link aktywacyjny jest aktywny przez 2 dni.</p>
                <p>Pozdrawiamy,<br>D&B&N Events</p>
            </div>
            <div id='footer'>
                © 2023 D&B&N Events. Wszelkie prawa zastrzeżone.
            </div>
        </body>
        </html>
    ";

    $headers = 'From: DBN_Events@wp.pl' . "\r\n" .
        'Reply-To: DBN_Events@wp.pl' . "\r\n" .
        'X-Mailer: PHP/' . phpversion() . "\r\n" .
        'MIME-Version: 1.0' . "\r\n" .
        'Content-type: text/html; charset=utf-8';

        if (mail($email, $message, $headers)) {
            return array('success' => true, 'message' => 'E-mail z kodem weryfikacyjnym został wysłany pomyślnie.');
        } else {
            return array('success' => false, 'message' => 'Błąd podczas wysyłania e-maila z kodem weryfikacyjnym.');
        }
    
}

?>

