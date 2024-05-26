<body>
    <?php
    session_start();
    $userLogin = isset($_SESSION['user_login']) ? $_SESSION['user_login'] : null;
    $userId = $_SESSION['user_id'];
    ?>

<style>
#logoutForm {
    display: flex;
    align-items: center;
    height: 40px;
    background-color: transparent;
    border: 1px solid transparent;
}

#logoutForm a {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #fff;
    margin-left: 10px;
    white-space: nowrap;
    vertical-align: middle;
    line-height: 1;
    height: 100%;
    font-size: 16px; 
}


#logoutForm i {
    font-size: 30px;
    vertical-align: middle;
    margin-bottom: 5px;
    margin-right: 10px; 
}

</style>
    <div class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="../A_Home/AdminHome.php">
                    <img src="../../assets/logo.png" alt="Logo firmy" class="mr-2 custom-logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="../A_Home/AdminHome.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../Admin/A_About/AdminAbout.php">About</a>
                        </li>
                        <li class="nav-item dropdown custom-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMyEvents" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                My Events
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMyEvents">
                                <li><a class="dropdown-item" href="../A_MyEvents/AdminAddEvent.php">Add Event</a></li>
                                <li><a class="dropdown-item" href="../A_MyEvents/AdminFavourites.php">Favourites</a>
                                </li>
                                <li><a class="dropdown-item" href="../A_MyEvents/AdminCreated.php">Created by me</a>
                                <li><a class="dropdown-item" href="../A_MyEvents/AdminArchiwum.php">Archiwum</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown custom-dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenu" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Menu
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenu">
                                <li><a class="dropdown-item" href="../A_Menu/AdminEditProfile.php?user_id=<?php echo  $userId;?>">Edit Profile</a></li>
                                <li><a class="dropdown-item" href="../A_Menu/AdminPanel.php">Admin Panel</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto"> 
                        <li class="nav-item">
                            <a class="nav-link" href="../A_Menu/UserProfile.php?user_id=<?php echo  $userId;?>&action=none">
                                <div>
                                    <?php echo 'Witaj ' . $userLogin; ?>
                                    <?php
                                    require_once "../../config/config.php";
                                    $connection = mysqli_connect($host, $user, $password, $dbname);
                                    
                                    if ($connection->connect_error) {
                                        die("Błąd połączenia: " . $connection->connect_error);
                                    }
                                    $sqlAvatar = "SELECT Photo FROM Events_Accounts WHERE UserId = $userId";
                                    $resultAvatar = $connection->query($sqlAvatar);

                                    if ($resultAvatar->num_rows > 0) {
                                        $rowAvatar = $resultAvatar->fetch_assoc();
                                        if (!empty($rowAvatar['Photo'])) {
                                            $imageData = base64_encode($rowAvatar['Photo']);
                                            $src = "data:image/jpeg;base64," . $imageData;
                                            echo "<img src='{$src}' alt='Avatar' class='avatar-icon' style='margin-left:10px; width: 50px; height: 50px; border-radius: 50%;'>";
                                        } else {
                                            echo "<img src='../../assets/Avatar.jpg' alt='Avatar' class='avatar-icon' style='margin-left:10px; width: 50px; height: 50px; border-radius: 50%;'>";
                                        }
                                    } else {
                                        echo "<img src='../../assets/Avatar.jpg' alt='Avatar' class='avatar-icon' style='margin-left:10px; width: 50px; height: 50px; border-radius: 50%;'>";
                                    }
                                    ?>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="../../Guest/G_LogIn/LogOut.php" method="post" id="logoutForm">
                                <a class="nav-link" href="#" onclick="document.getElementById('logoutForm').submit()">
                                    <i class="fas fa-sign-out-alt fa-lg"></i>
                                    Log out
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>