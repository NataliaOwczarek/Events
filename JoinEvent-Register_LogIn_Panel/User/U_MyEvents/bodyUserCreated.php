<?php
session_start();
require_once "../../config/config.php";
require_once "../../functions/db_fun.php";
$userId = $_SESSION['user_id'];

$connection = mysqli_connect($host, $user, $password, $dbname);
if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

$sql = "SELECT * FROM Events_ListOfEvents WHERE UserId = $userId ORDER BY EventDateTime desc";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<div style='margin-top: 70px; margin-bottom: 70px;' class='container mt-8'>";
    echo "<div id='userEvents' class='row row-cols-1 row-cols-md-3 g-4'>";

    while ($row = $result->fetch_assoc()) {
        $eventId = $row['EventId'];

        echo "<div id='eventCard{$eventId}' class='col'>";
        echo "<div class='card event-card bg-light'>";


        echo "<p class='card-text text-center'>Lokalizacja: {$row['EventLocation']}</p>";

        if (!empty($row['EventImage'])) {
            $src = "data:image/jpeg;base64," . base64_encode($row['EventImage']);
            echo "<img src='{$src}' class='card-img-top' alt='Zdjęcie wydarzenia' style='max-width: 300px; height: 300px; display: block; margin: 0 auto;'>";
        } else {
            echo "<img src='../../assets/Events.png' class='card-img-top' alt='Zdjęcie wydarzenia' style='max-width: 300px; height: 300px; display: block; margin: 0 auto;'>";
        }

        echo "<div class='col text-center' style='margin-top: 15px;'>";
        echo "<h2 class='card-title'>{$row['EventTitle']}</h2>";

        $eventDateTime = new DateTime($row['EventDateTime']);
        $locale = 'pl_PL';
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Europe/Warsaw');
        $formattedDate = $formatter->format($eventDateTime);

        $hourAndMinute = $eventDateTime->format('H:i');

        echo "<p class='card-text'>Data: {$formattedDate}, godzina {$hourAndMinute}</p>";
        echo "<a href='../U_Event/UserEventDetails.php?id={$row['EventId']}' class='btn btn-secondary'>Szczegóły wydarzenia</a>";
        echo "<button id='removeBtn{$eventId}' class='btn btn-favorite btn-remove-event' style='margin-top:0px;' data-event-id='{$row['EventId']}' onclick='confirmDelete($eventId)'>";
        echo '<i class="bi bi-trash-fill" style="font-size:30px;"></i>';
        echo '</button>';
        echo "<div class='card-body text-center'>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    echo "</div>";
    echo "</div>";

    if ($result->num_rows === 0) {
        echo "<div class='container mt-5'>";
        echo "<div class='row justify-content-center'>";
        echo "<div class='col-md-8 text-center'>";
        echo "<div class='alert alert-secondary' role='alert'>";
        echo "<h4 class='alert-heading'>Brak wydarzeń!</h4>";
        echo "<p>Nie stworzyłeś jeszcze żadnych wydarzeń. Dodaj je teraz!</p>";
        echo "<a href='../U_MyEvents/UserAddEvent.php' class='btn btn-primary' style='background-color: #444; color: #fff; border: none;'>Stwórz wydarzenie</a>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<div class='container mt-5'>";
    echo "<div class='row justify-content-center'>";
    echo "<div class='col-md-8 text-center'>";
    echo "<div class='alert alert-secondary' role='alert'>";
    echo "<h4 class='alert-heading'>Brak wydarzeń!</h4>";
    echo "<p>Nie stworzyłeś jeszcze żadnych wydarzeń. Dodaj je teraz!</p>";
    echo "<a href='../U_MyEvents/UserAddEvent.php' class='btn btn-primary' style='background-color: #444; color: #fff; border: none;'>Stwórz wydarzenie</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

mysqli_close($connection);
?>
<script>
function confirmDelete(eventId) {
    if (confirm("Czy na pewno chcesz usunąć to wydarzenie?")) {
        deleteEvent(eventId);
    }
}

async function deleteEvent(eventId) {
    try {
        const response = await fetch(`../U_Event/RemoveEvent.php?eventId=${eventId}`);
        const result = await response.text();


        const eventCard = document.getElementById(`eventCard${eventId}`);
        if (eventCard) {
            eventCard.remove();
        }


        const remainingEventCards = document.querySelectorAll('.card.event-card');
        if (remainingEventCards.length === 0) {
            const noUserEventsMsg = document.createElement('div');
            noUserEventsMsg.classList.add('container', 'mt-5');
            noUserEventsMsg.innerHTML = `
                <div class='row justify-content-center'>
                    <div class='col-md-8 text-center'>
                        <div class='alert alert-secondary' role='alert'>
                            <h4 class='alert-heading'>Brak wydarzeń!</h4>";
                            <p>Nie stworzyłeś jeszcze żadnych wydarzeń.</p>";
                            <a href='../U_MyEvents/UserAddEvent.php' class='btn btn-primary' style='background-color: #444; color: #fff; border: none;'>Stwórz wydarzenie</a>";
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('userEvents').replaceWith(noUserEventsMsg);
        }
    } catch (error) {
        console.error('Wystąpił błąd:', error);
    }
}
</script>
