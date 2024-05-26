<?php
session_start();
require_once "../../config/config.php";
require_once "../../functions/db_fun.php";
$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}
$myID = $_SESSION['user_id'];
$user_id = $_GET['user_id'];
$action = $_GET['action'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_FILES["newPhoto"]["tmp_name"]) && is_uploaded_file($_FILES["newPhoto"]["tmp_name"])) {
        $newPhoto = file_get_contents($_FILES["newPhoto"]["tmp_name"]);
        $sqlUpdatePhoto = "UPDATE Events_Accounts SET Photo = ? WHERE UserId = ?";
        $stmt = $connection->prepare($sqlUpdatePhoto);
        $null = null;
        $stmt->bind_param("bi", $null, $user_id);
        $stmt->send_long_data(0, $newPhoto); 
        if ($stmt->execute()) {?>
        <script>
            window.location.href = 'UserProfile.php?user_id=<?php echo $user_id; ?>&action=none';
        </script>
        <?php
        } else {
            echo "Błąd podczas zmiany zdjęcia profilowego: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Błąd przesyłania pliku.";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if ($action == 'deleteProfile') {
        $sqlDeleteProfile = "UPDATE Events_Accounts SET IsActive = 0 WHERE UserId = $user_id";
        $resultDeleteProfile = $connection->query($sqlDeleteProfile);

        if ($resultDeleteProfile->num_rows > 0) {
            $rowDeleteProfile = $resultDeleteProfile->fetch_assoc();
        }
        if ($user_id == $myID) {
            addLog($myId, 'Log out', $connection); ?>
            <script>
                window.location.href = '../../index.php';
            </script>
            <?php
        } else { ?>
            <script>
                window.location.href = 'AdminPanel.php';
            </script>
        <?php }
    } elseif ($action == 'deletePhoto') {
        $sqlDeletePhoto = "UPDATE Events_Accounts SET Photo = ? WHERE UserId = ?";
        $stmt = $connection->prepare($sqlDeletePhoto);
        $null = null;
        $stmt->bind_param("bi", $null, $user_id);
        if ($stmt->execute()) {
            echo "Zdjęcie profilowe zostało usunięte.";
        } else {
            echo "Błąd podczas usuwania zdjęcia profilowego: " . $stmt->error;
        }
        $stmt->close(); ?>
        <script>
            window.location.href = 'UserProfile.php?user_id=<?php echo $user_id; ?>&action=none';
        </script>
    <?php }

    $sqlUserProfile = "SELECT 
                    U.UserId,
                    U.Name,
                    U.Surname,
                    U.Sex,
                    U.Date_of_birth,
                    A.Email,
                    A.Photo,
                    A.IsAdmin
                FROM
                    Events_Users U
                INNER JOIN
                    Events_Accounts A ON U.UserId = A.UserId
                WHERE U.UserId = $user_id";

    $resultUserProfile = $connection->query($sqlUserProfile);

    if ($resultUserProfile->num_rows > 0) {
        $rowUserProfile = $resultUserProfile->fetch_assoc();
        $IsAdmin = $rowUserProfile['IsAdmin'];
        echo '<div style="width: 50%; display: flex; justify-content: space-between; margin-top: 50px; ">';

        echo '<div style="width: 70%; background-color: #f2f2f2; padding: 20px; border-radius: 10px; display: flex; flex-direction: column; align-items: center;">';

        if (!empty($rowUserProfile['Photo'])) {
            $imageData = base64_encode($rowUserProfile['Photo']);
            $src = "data:image/jpeg;base64," . $imageData;

            echo "<div style='position: relative;'>";
            echo "<img src='{$src}' class='card-img-top' alt='Zdjęcie użytkownika' style='max-width: 200px; height: 200px; display: block; margin: 0 auto;'></img>";
            echo "<div style='position: absolute; top: 10px; right: 10px;'>";
            echo "</div>";
            echo "</div>";

            echo "<hr style='border-top: 2px solid black; margin: 10px 0;'>";

        } else {
            echo "<img src='../../assets/Avatar.jpg' class='card-img-top' alt='Zdjęcie wydarzenia' style='max-width: 200px; height: 200px; display: block; margin: 0 auto;'>";

            echo "<hr style='border-top: 2px solid black; margin: 10px 0;'>";
        }

        echo "<h2>{$rowUserProfile['Name']} {$rowUserProfile['Surname']}</h2>";
        echo "<p>Email: {$rowUserProfile['Email']}</p>";
        echo "<p>Płeć: {$rowUserProfile['Sex']}</p>";
        echo "<p>Data urodzenia: {$rowUserProfile['Date_of_birth']}</p>";
        echo "<div>";
        echo "<form method='post' enctype='multipart/form-data' style='background-color: transparent; border-color: transparent;'>";
        echo "<input type='file' name='newPhoto'>";
        echo "<button type='submit'>Zmień zdjęcie</button>";
        echo "</form>";
        echo "</div>";
        echo '</div>';

        echo '<div style="width: 25%; background-color: #f2f2f2; padding: 20px; border-radius: 10px;">';

        echo '<div style="display: flex; flex-direction: column; align-items: center;">';
        echo "<a href='../A_MyEvents/AdminCreated.php' class='btn btn-secondary' style='width: 150px; margin-bottom: 10px;'>Moje wydarzenia</a>";
        echo "<a href='../A_MyEvents/AdminFavourites.php' class='btn btn-secondary' style='width: 150px; margin-bottom: 10px;'>Ulubione</a>";
        echo "<a href='../A_MyEvents/AdminArchiwum.php' class='btn btn-secondary' style='width: 150px; margin-bottom: 30px;'>Archiwum</a>";
 
        echo "<button onclick='deletePhoto()' class='btn btn-secondary' style='width: 150px; margin-bottom: 40px;'>Usuń zdjęcie</button>";

        echo "<button onclick='editProfile()' class='btn btn-secondary' style='width: 150px; margin-bottom: 10px;'>Edytuj profil</button>";
        if ($IsAdmin == 0) {
            echo "<button onclick='deleteProfile()' class='btn btn-danger' style='width: 150px;'>Usuń profil</button>";
        }


        echo '</div>';

        echo '</div>';
        echo '</div>';

    } else {
        echo "Nie znaleziono użytkownika o podanym ID.";
    }

    $connection->close();
}
?>



<script>


    function deletePhoto() {
        if (confirm("Czy na pewno chcesz usunąć zdjęcie profilowe?")) {
            window.location.href = 'UserProfile.php?user_id=<?php echo $user_id; ?>&action=deletePhoto';
        }
    }

    function editProfile() {
        window.location.href = 'AdminEditProfile.php?user_id=<?php echo $user_id; ?>';
    }

    function deleteProfile() {
        if (confirm("Czy na pewno chcesz usunąć konto?")) {
            window.location.href = 'UserProfile.php?user_id=<?php echo $user_id; ?>&action=deleteProfile';
        }
    }
</script>