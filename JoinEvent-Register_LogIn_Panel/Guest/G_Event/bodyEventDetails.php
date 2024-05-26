

    <link rel="stylesheet" href="../../css/styleEvent.css">


<div class="container mt-5">
    <div class="row">
        <?php
        require_once "../../config/config.php";
        $connection = mysqli_connect($host, $user, $password, $dbname);

        if ($connection->connect_error) {
            die("Błąd połączenia: " . $connection->connect_error);
        }

        $eventIDToShow = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $sql = "SELECT Events_ListOfEvents.*, Events_Users.Name AS Organizer 
                FROM Events_ListOfEvents 
                LEFT JOIN Events_Users ON Events_ListOfEvents.UserId = Events_Users.UserId
                WHERE Events_ListOfEvents.EventDateTime > NOW()
                AND Events_ListOfEvents.EventId = $eventIDToShow 
                ORDER BY Events_ListOfEvents.EventDateTime";

        $result = $connection->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "<div class='col-md-12'>";
            echo "<div class='card event-card'>"; 

            echo "<div class='card-body'>";
            
            if (!empty($row['EventImage'])) {
                $imageData = base64_encode($row['EventImage']);
                $src = "data:image/jpeg;base64,{$imageData}";
                echo "<img src='{$src}' class='event-image img-fluid' alt='Zdjęcie wydarzenia'>";
            }
            else{
                echo "<img src='../../assets/Events.png' class='event-image img-fluid' alt='Zdjęcie wydarzenia' >";
               }

            echo "<div class='event-details'>";
            echo "<h2 class='event-title'>{$row['EventTitle']}</h2>";
            $eventDateTime = new DateTime($row['EventDateTime']);
            $locale = 'pl_PL';
            $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Warsaw');
            $formattedDate = $formatter->format($eventDateTime);
            $hourAndMinute = $eventDateTime->format('H:i');
            echo "<p class='card-text'> " . $formattedDate . ", godzina " . $hourAndMinute . "</p>";
            echo "<p><strong>Lokalizacja:</strong> {$row['EventLocation']}</p>";
            echo "<p><strong>Organizator:</strong> {$row['Organizer']}</p>";
            echo "<p><strong>Opis:</strong> {$row['EventDescription']}</p>";

            echo "<table class='participants-table'>";
            echo "<tr><th>Uczestnicy</th></tr>";
            $participantsQuery = "SELECT Events_EventMembers.*,  Events_Accounts.Login
                FROM Events_EventMembers
                LEFT JOIN Events_Accounts ON Events_EventMembers.UserId = Events_Accounts.UserId
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

