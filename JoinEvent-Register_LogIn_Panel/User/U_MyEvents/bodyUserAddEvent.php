<?php
require_once "../../config/config.php";
$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

$userId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eventTitle = $_POST["eventTitle"];
    $eventDateTime = $_POST["eventDateTime"];
    $eventLocation = $_POST["eventLocation"];
    $eventDescription = $_POST["eventDescription"];

    if (!empty($_FILES["eventPhoto"]["tmp_name"]) && is_uploaded_file($_FILES["eventPhoto"]["tmp_name"])) {
        $eventPhoto = file_get_contents($_FILES["eventPhoto"]["tmp_name"]);
    } else {
        echo "Błąd przesyłania pliku.";
        exit;
    }

    $sqlAddEvent = "INSERT INTO Events_ListOfEvents (UserId, EventTitle, EventDateTime, EventLocation, EventDescription, EventImage) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sqlAddEvent);

    $null = null;
    $stmt->bind_param("sssssb", $userId, $eventTitle, $eventDateTime, $eventLocation, $eventDescription, $null);
    $stmt->send_long_data(5, $eventPhoto);

    if ($stmt->execute()) {
        echo "Event created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
}
?>

<div class="container mt-5">
    <div class="rounded p-4 border">
        <h2 class="mb-4">Dodaj event</h2>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="eventTitle">Event Title:</label>
                <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                    placeholder="Enter event title" required>
            </div>

            <div class="form-group">
                <label for="eventDateTime">Event Date and Time:</label>
                <input type="datetime-local" class="form-control" id="eventDateTime" name="eventDateTime" required>
            </div>

            <div class="form-group">
                <label for="eventLocation">Event Location:</label>
                <input type="text" class="form-control" id="eventLocation" name="eventLocation"
                    placeholder="Enter event location" required>
            </div>

            <div class="form-group">
                <label for="eventDescription">Event Description:</label>
                <textarea class="form-control" id="eventDescription" name="eventDescription" rows="3"
                    placeholder="Enter event description"></textarea>
            </div>
            <div class="form-group">
                <label for="eventPhoto">Event Photo: [photo should be in size: 300 x 300 to proper display]</label>
                <input type="file" class="form-control-file" id="eventPhoto" name="eventPhoto" accept="image/*">
            </div>

            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>