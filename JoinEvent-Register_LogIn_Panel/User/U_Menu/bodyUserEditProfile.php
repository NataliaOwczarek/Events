<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../../config/config.php";
require_once "../../functions/validation_fun.php";
$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

$userId = $_SESSION["user_id"];

$existingLogin = $existingEmail = "";

$sqlSelectUser = "SELECT Login, Email FROM Events_Accounts WHERE UserId = ?";
$stmtSelect = $connection->prepare($sqlSelectUser);
$stmtSelect->bind_param("i", $userId);
$stmtSelect->execute();
$stmtSelect->bind_result($existingLogin, $existingEmail);
$stmtSelect->fetch();
$stmtSelect->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newLogin = $_POST["newLogin"];
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];
    $newEmail = $_POST["newEmail"];

    if ($newPassword != $confirmPassword) {
        echo '<script>alert("Podane hasła różnią się od siebie.");</script>';
        echo '<script>window.location.href = "../U_Menu/UserEditProfile.php";</script>';
        exit;
    }

    if (!validateEmailFormat($newEmail)) {
        echo '<script>alert("Nieprawidłowy adres email.");</script>';
        echo '<script>window.location.href = "../U_Menu/UserEditProfile.php";</script>';
        exit;
    }

    if (!validatePassword($newPassword)) {
        echo '<script>alert("Hasło nie spełnia wymagań.");</script>';
        echo '<script>window.location.href = "../U_Menu/UserEditProfile.php";</script>';
        exit;
    }

    if (!isLoginUnique($newLogin, $connection)) {
        echo '<script>alert("Login jest już zajęty.");</script>';
        echo '<script>window.location.href = "../U_Menu/UserEditProfile.php";</script>';
        exit;
    }

    if (!isEmailUnique($newEmail, $connection)) {
        echo '<script>alert("W bazie istnieje użytkownik o podanym adresie email.");</script>';
        echo '<script>window.location.href = "../U_Menu/UserEditProfile.php";</script>';
        exit;
    }

    $sqlUpdateUser = "UPDATE Events_Accounts SET Login=?, Password=?, Email=? WHERE UserId=?";
    $stmt = $connection->prepare($sqlUpdateUser);

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt->bind_param("sssi", $newLogin, $newPassword, $newEmail, $userId);

    if ($stmt->execute()) {
        echo '<script>alert("Profil został zaktualizowany pomyślnie.");</script>';
    } else {
        echo '<script>alert("Error: ' . $stmt->error . '");</script>';
    }

    $stmt->close();
    $connection->close();
}
$sqlSelectUserInfo = "SELECT UserId, Name, Surname, Sex, Date_of_birth FROM Events_Users WHERE UserId = ?";
$stmtUserInfo = $connection->prepare($sqlSelectUserInfo);
$stmtUserInfo->bind_param("i", $userId);
$stmtUserInfo->execute();
$stmtUserInfo->bind_result($userId, $name, $surname, $sex, $dateOfBirth);
$stmtUserInfo->fetch();
$stmtUserInfo->close();
?>


<div class="container mt-5">
    <div class="rounded p-4 border">
        <h2 class="mb-4">Dane użytkownika</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="border p-2">
                    <?php
                    $sqlSelectPhoto = "SELECT Photo FROM Events_Accounts WHERE UserId = ?";
                    $stmtPhoto = $connection->prepare($sqlSelectPhoto);
                    $stmtPhoto->bind_param("i", $userId);
                    $stmtPhoto->execute();
                    $stmtPhoto->store_result();

                    if ($stmtPhoto->num_rows > 0) {
                        $stmtPhoto->bind_result($photo);
                        $stmtPhoto->fetch();
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($photo) . '" class="img-fluid" alt="Zdjęcie użytkownika">';
                    } else {
                        echo '<img src="../../assets/Avatar.jpg" class="img-fluid" alt="Domyślne zdjęcie użytkownika">';
                    }

                    $stmtPhoto->close();
                    ?>
                </div>
            </div>
            <div class="col-md-9">
                <p><strong>Imię:</strong> <?php echo $name; ?></p>
                <p><strong>Nazwisko:</strong> <?php echo $surname; ?></p>
                <p><strong>Płeć:</strong> <?php echo $sex; ?></p>
                <p><strong>Data urodzenia:</strong> <?php echo $dateOfBirth; ?></p>
            </div>
        </div>

        <hr>
    </div>
 </div>
<div class="container mt-5">
    <div class="rounded p-4 border">
        <h2 class="mb-4">Edytuj profil</h2>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="form-group">
                <label for="newLogin">New Login:</label>
                <input type="text" class="form-control" id="newLogin" name="newLogin" placeholder="Enter new login"
                    value="<?php echo $existingLogin; ?>" required>
            </div>

            <div class="form-group">
                <label for="newEmail">New Email:</label>
                <input type="email" class="form-control" id="newEmail" name="newEmail" placeholder="Enter new email"
                    value="<?php echo $existingEmail; ?>" required>
            </div>

            <div class="form-group">
                <label for="newPassword">New Password:</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword"
                    placeholder="Enter new password" required>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                    placeholder="Confirm new password" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
            <button type="button" class="btn btn-danger">Usuń profil</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>