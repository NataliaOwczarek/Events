<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/styleEvent.css">
    <title>Event Details</title>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <?php
        session_start();
        $userId = $_SESSION['user_id'];
        require_once "../../config/config.php";
        require_once "../../functions/db_fun.php";
        $connection = mysqli_connect($host, $user, $password, $dbname);

        if ($connection->connect_error) {
            die("Błąd połączenia: " . $connection->connect_error);
        }

        $eventIDToShow = intval($_GET['id']);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $newEventTitle = $_POST["newEventTitle"];
            $newEventDateTime = $_POST["newEventDateTime"];
            $newEventLocation = $_POST["newEventLocation"];
            $newEventDescription = $_POST["newEventDescription"];
            $newEventImage = $_POST["newEventImage"];
            $eventId = $_POST["eventId"];

            if (!empty($_FILES["newEventPhoto"]["tmp_name"]) && is_uploaded_file($_FILES["newEventPhoto"]["tmp_name"])) {
                $newEventPhoto = file_get_contents($_FILES["newEventPhoto"]["tmp_name"]);

                $updateEventQuery = "UPDATE Events_ListOfEvents
                    SET EventTitle = ?, EventDateTime = ?, EventLocation = ?, EventDescription = ?, EventImage = ?
                    WHERE EventId = ?";
                $stmt = $connection->prepare($updateEventQuery);
                $stmt->bind_param("sssssi", $newEventTitle, $newEventDateTime, $newEventLocation, $newEventDescription, $newEventPhoto, $eventId);
            } else {
                $updateEventQuery = "UPDATE Events_ListOfEvents
                    SET EventTitle = ?, EventDateTime = ?, EventLocation = ?, EventDescription = ?
                    WHERE EventId = ?";
                $stmt = $connection->prepare($updateEventQuery);
                $stmt->bind_param("ssssi", $newEventTitle, $newEventDateTime, $newEventLocation, $newEventDescription, $eventId);
            }

            if ($stmt->execute()) {
                echo "<script>window.location.href = 'AdminEventDetails.php?id=" . $eventId . "';</script>";
                exit;
            } else {
                echo "Error updating event details: " . $stmt->error;
            }

            $stmt->close();
        }

        $sql = "SELECT Events_ListOfEvents.*, Events_Users.*, Events_Accounts.* 
                FROM Events_ListOfEvents 
                LEFT JOIN Events_Users ON Events_ListOfEvents.UserId = Events_Users.UserId 
                LEFT JOIN Events_Accounts ON Events_Accounts.UserId = Events_Users.UserId
                WHERE Events_ListOfEvents.EventId = $eventIDToShow 
                ORDER BY Events_ListOfEvents.EventDateTime";

        $result = $connection->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<div class='col-md-12'>";
            echo "<div class='card event-card'>";

            echo "<div style='text-align: right; margin-top:20px; margin-right:30px;'>";

            $eventDateTime = new DateTime($row['EventDateTime']);
            $currentDateTime = new DateTime();

            if ($eventDateTime >= $currentDateTime) {
                if (in_array($row['EventId'], array_values(getSignedUpEventsByUser($userId, $connection)))) {
                    echo "<button class='btn btn-danger btn-save' data-SingUpEvent-id='{$row["EventId"]}' onclick='toggleSingUpForEvent(this, {$row["EventId"]})'>Wypisz się</button>";
                } else {
                    echo "<button class='btn btn-success btn-save' data-SingUpEvent-id='{$row["EventId"]}' onclick='toggleSingUpForEvent(this, {$row["EventId"]})'>Zapisz się</button>";
                }

                echo "<button class='btn btn-favorite' data-event-id='{$row['EventId']}' onclick='toggleFavorite(this, {$row['EventId']})'>";
                if (in_array($row['EventId'], array_values(getFavouriteEventsbyUser($userId, $connection)))) {
                    echo '<i class="bi bi-x-circle-fill" style="font-size:24px; "></i>';
                } else {
                    echo '<i class="bi bi-heart-fill" style="font-size:24px;"></i>';
                }
                echo '</button>';
            } else {
                echo "Wydarzenie już się odbyło";
            }

            echo "</div>";

            echo "<div class='card-body'>";

            if (!empty($row['EventImage'])) {
                $imageData = base64_encode($row['EventImage']);
                $src = "data:image/jpeg;base64,{$imageData}";
                echo "<img src='{$src}' class='event-image img-fluid' alt='Zdjęcie wydarzenia'>";
            } else {
                echo "<img src='../../assets/Events.png' class='event-image img-fluid' alt='Zdjęcie wydarzenia' >";
            }

            echo "<div class='event-details'>";
            echo "<h2 class='event-title'>{$row["EventTitle"]}";


                echo "<button class='btn btn-primary' style='margin-left:20px;' id='editButton' data-toggle='modal' data-target='#editEventModal'>Edit</button>";
                echo "<button class='btn btn-remove' style='margin-left:10px;' data-event-id='{$row['EventId']}' onclick='removeEvent(this, {$row['EventId']})'>";
                echo '<i class="bi bi-trash-fill" style="font-size:24px;"></i>';
                echo '</button>';

            echo '</h2>';

            $eventDateTime = new DateTime($row['EventDateTime']);
            $locale = 'pl_PL';
            $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Warsaw');
            $formattedDate = $formatter->format($eventDateTime);
            $hourAndMinute = $eventDateTime->format('H:i');
            echo "<p class='card-text'> " . $formattedDate . ", godzina " . $hourAndMinute . "</p>";
            echo "<p><strong>Lokalizacja:</strong> {$row['EventLocation']}</p>";
            echo "<p><strong>Organizator:</strong> {$row['Name']}</p>";
            echo "<p><strong>Opis:</strong> {$row['EventDescription']}</p>";

            echo "<table class='participants-table'>";
            echo "<tr><th>Uczestnicy</th></tr>";
            $participantsQuery = "SELECT Events_EventMembers.*,  Events_Accounts.*
                FROM Events_EventMembers
                INNER JOIN Events_Accounts ON Events_EventMembers.UserId = Events_Accounts.UserId
                WHERE Events_EventMembers.EventId = $eventIDToShow
                AND Events_Accounts.IsActive=1";

            $participantsResult = $connection->query($participantsQuery);

            if ($participantsResult && $participantsResult->num_rows > 0) {
                while ($participantRow = $participantsResult->fetch_assoc()) {
                    echo "<tr><td>{$participantRow['Login']}</td></tr>";
                }
            } else {
                echo "<tr><td>Brak uczestników.</td></tr>";
            }
            echo "</table>";

            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";

            echo "<div class='modal fade' id='editEventModal' tabindex='-1' role='dialog' aria-labelledby='editEventModalLabel' aria-hidden='true'>";
            echo "<div class='modal-dialog' role='document'>";
            echo "<div class='modal-content'>";
            echo "<div class='modal-header'>";
            echo "<h5 class='modal-title' id='editEventModalLabel'>Edit Event</h5>";
            echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
            echo "<span aria-hidden='true'>&times;</span>";
            echo "</button>";
            echo "</div>";
            echo "<div class='modal-body'>";

            echo "<form action='' method='post'  enctype='multipart/form-data'>";
            echo "<label for='newEventTitle'>New Event Title:</label>";
            echo "<input type='text' id='newEventTitle' name='newEventTitle' value='{$row['EventTitle']}' required><br>";

            echo "<label for='newEventDateTime'>New Event Date and Time:</label>";
            echo "<input type='datetime-local' id='newEventDateTime' name='newEventDateTime' value='{$row['EventDateTime']}' required><br>";

            echo "<label for='newEventLocation'>New Event Location:</label>";
            echo "<input type='text' id='newEventLocation' name='newEventLocation' value='{$row['EventLocation']}' required><br>";

            echo "<label for='newEventDescription'>New Event Description:</label>";
            echo "<textarea id='newEventDescription' name='newEventDescription' rows='6' cols='40' required>{$row['EventDescription']}</textarea><br>";

            echo "<label for='newEventPhoto'>New Event Photo: [photo should be in size: 300 x 300 to proper display]</label>";
            echo "<input type='file' id='newEventPhoto ' name='newEventPhoto'><br>";

            echo "<input type='hidden' name='eventId' value='{$row['EventId']}'>";
            echo "<div class='text-center'>";
            echo "<button type='submit' class='btn btn-primary'>Save Changes</button>";
            echo "</div>";
            echo "</form>";

            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='col text-center'>";
            echo "<p class='mt-5'>Brak dostępnych wydarzeń.</p>";
            echo "</div>";
        }
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"> </script>
<script>

    async function toggleFavorite(button, eventId) {
        try {
            var response = await fetch(`../A_Home/AdmintoggleFavourite.php?eventId=${eventId}`);
            var result = await response.text();

            var heartIcon = button.querySelector('i.bi-heart-fill');

            if (!heartIcon) {
                heartIcon = button.querySelector('i.bi-x-circle-fill');
            }

            if (heartIcon) {
                if (result === 'added') {
                    heartIcon.classList = 'bi bi-x-circle-fill';
                } else if (result === 'removed') {
                    heartIcon.classList = 'bi bi-heart-fill';
                }
            } else {
                console.error('Nie znaleziono ikony serca lub kółka.');
            }
        } catch (error) {
            console.error('Wystąpił błąd:', error);
        }
    }

    async function toggleSingUpForEvent(button, eventId) {
        try {
            var response = await fetch(`../A_Home/AdmintoggleSingUpForEvent.php?eventId=${eventId}`);
            var result = await response.text();

            if (result === 'added') {
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
                button.textContent = 'Wypisz się';
            } else if (result === 'removed') {
                button.classList.remove('btn-danger');
                button.classList.add('btn-success');
                button.textContent = 'Zapisz się';
            }
            location.reload();
        } catch (error) {
            console.error('Wystąpił błąd:', error);
        }
    }

    async function removeEvent(button, eventId) {
        var confirmation = confirm("Czy na pewno chcesz usunąć to wydarzenie?");

        if (confirmation) {
            try {
                var response = await fetch(`RemoveEvent.php?eventId=${eventId}`);
                var result = await response.text();

                if (result === 'removed') {
                    window.location.href = '../A_Home/AdminHome.php';
                } else {
                    console.error('Nie udało się usunąć wydarzenia.');
                }
            } catch (error) {
                console.error('Wystąpił błąd:', error);
            }
        } else {
            window.location.href = `AdminEventDetails.php?id=${eventId}`;
        }
    }

</script>

</body>
</html>
