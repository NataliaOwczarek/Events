<?php
session_start();
require_once "../../config/config.php";
require_once "../../functions/db_fun.php";
$userId = $_SESSION['user_id'];

$connection = mysqli_connect($host, $user, $password, $dbname);
if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}


$currentDate = date('Y-m-d H:i:s');
$sql = "SELECT e.* FROM Events_ListOfEvents e
        INNER JOIN Events_EventMembers em ON e.EventId = em.EventId
        WHERE em.UserId = $userId AND e.EventDateTime < '$currentDate'
        ORDER BY EventDateTime desc";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<div style='margin-top: 70px; margin-bottom: 70px;' class='container mt-8'>";
    echo "<div id='favoriteEvents' class='row row-cols-1 row-cols-md-3 g-4'>";

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


        echo '</button>';
        echo "<div class='card-body text-center'>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='container mt-5'>";
    echo "<div class='row justify-content-center'>";
    echo "<div class='col-md-8 text-center'>";
    echo "<div class='alert alert-secondary' role='alert'>";
    echo "<h4 class='alert-heading'>Brak uczestnictwa w przeszłych wydarzeniach!</h4>";
    echo "<p>Nie uczestniczyłeś jeszcze w żadnych przeszłych wydarzeniach.</p>";
    echo "<a href='../U_Home/UserHome.php' class='btn btn-primary' style='background-color: #444; color: #fff; border: none;'>Przejdź do strony głównej</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

mysqli_close($connection);
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-remove-favorite').forEach(button => {
        button.addEventListener('click', async function(event) {
            event.preventDefault();
            const eventId = this.getAttribute('data-event-id');

            try {
                const response = await fetch(`../../User/U_Home/toggleFavourite.php?eventId=${eventId}`);
                const result = await response.text();

                const eventCards = document.querySelectorAll('.card.event-card');
                if (result === 'removed') {
                    const eventCard = document.getElementById(`eventCard${eventId}`);
                    if (eventCard) {
                        eventCard.remove();
                    }
                }

                const remainingEventCards = document.querySelectorAll('.card.event-card');
                if (remainingEventCards.length === 0) {

                    const noFavoriteEventsMsg = document.createElement('div');
                    noFavoriteEventsMsg.classList.add('container', 'mt-5');
                    noFavoriteEventsMsg.innerHTML = `
                        <div class='row justify-content-center'>
                            <div class='col-md-8 text-center'>
                                <div class='alert alert-secondary' role='alert'>
                                    <h4 class='alert-heading'>Brak uczestnictwa w przeszłych wydarzeniach!</h4>";
                                    <p>Nie uczestniczyłeś jeszcze w żadnych przeszłych wydarzeniach.</p>
                                    <a href='../U_Home/UserHome.php' class='btn btn-primary' style='background-color: #444; color: #fff; border: none;'>Przejdź do strony głównej</a>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('favoriteEvents').replaceWith(noFavoriteEventsMsg);
                }
            } catch (error) {
                console.error('Wystąpił błąd:', error);
            }
        });
    });
});
</script>
