<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>D&B&N Events</title>
    <link rel="icon" href="assets/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <div class="header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="#">
                    <img src="assets/logo.png" alt="Logo firmy" class="mr-2 custom-logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Guest/G_About/GuestAbout.php">About</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="Guest/G_LogIn/GuestLogIn.php">Log In/ Register</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>

    <div style="margin-top: 70px;" class="container mt-8">
        <div class="row row-cols-1 row-cols-md-3 g-4">

            <?php
            setlocale(LC_TIME, 'pl_PL.utf8');

            require_once "config/config.php";
            $connection = mysqli_connect($host, $user, $password, $dbname);

            if ($connection->connect_error) {
                die("Błąd połączenia: " . $connection->connect_error);
            }

            $today = date("Y-m-d H:i:s");
            $sql = "SELECT * FROM Events_ListOfEvents WHERE EventDateTime > '$today' ORDER BY EventDateTime";
            $result = $connection->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='col'>";
                    echo "<div class='card event-card bg-light'>";

                    echo "<p class='card-text text-center'>Lokalizacja: {$row['EventLocation']}</p>";

                    if (!empty($row['EventImage'])) {
                        $imageData = base64_encode($row['EventImage']);
                        $src = "data:image/jpeg;base64," . $imageData;
                        echo "<img src='{$src}' class='card-img-top' alt='Zdjęcie wydarzenia' style='max-width: 300px; height: 300px; display: block; margin: 0 auto;'>";
                    } else {
                        echo "<img src='assets/Events.png' class='card-img-top' alt='Zdjęcie wydarzenia' style='max-width: 300px; height: 300px; display: block; margin: 0 auto;'>";
                    }

                    echo "<div class='col text-center' style='margin-top: 15px;'>";
                    echo "<h2 class='card-title'>{$row['EventTitle']}</h2>";
                    $eventDateTime = new DateTime($row['EventDateTime']);
                    $locale = 'pl_PL';
                    $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Warsaw');
                    $formattedDate = $formatter->format($eventDateTime);
                    $hourAndMinute = $eventDateTime->format('H:i');
                    echo "<p class='card-text'> " . $formattedDate . ", godzina " . $hourAndMinute . "</p>";
                    echo "<a href='Guest/G_Event/EventDetails.php?id={$row['EventId']}' class='btn btn-secondary'>Szczegóły wydarzenia</a>";
                    echo "</div>";
                    echo "<div class='card-body text-center'>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='col text-center bg-light'>";
                echo "Brak dostępnych wydarzeń.";
                echo "</div>";
            }
            ?>
        </div>
    </div>
    <?php include('include/main.php'); ?>
    <?php include('include/footer.php'); ?>
</body>

</html>
