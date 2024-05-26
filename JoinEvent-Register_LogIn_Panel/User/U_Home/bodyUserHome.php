<div style="margin-top: 70px; margin-bottom: 70px;" class="container mt-8">

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="mb-3 p-2 rounded border bg-transparent" style="max-width: 300px;">

<button type="submit" name="filterEvents" class="btn btn-secondary btn-sm" style="margin-bottom: 20px">Filtruj: Wydarzenia, na które się zapisałeś</button>

<div></div>
<?php if (isset($_POST['filterEvents'])) : ?>
    <button type="submit" name="resetFilter" class="btn btn-secondary btn-sm ml-2">Cofnij filtrowanie</button>
<?php endif; ?>
</form>

    <div class="row row-cols-1 row-cols-md-3 g-4">



    <?php
    require_once "../../config/config.php";
    require_once "../../functions/db_fun.php";
    $userId = $_SESSION['user_id'];
    $connection = mysqli_connect($host, $user, $password, $dbname);

    if ($connection->connect_error) {
        die("Błąd połączenia: " . $connection->connect_error);
    }

    $today = date("Y-m-d H:i:s");

    if (isset($_POST['resetFilter'])) {
       
        $sql = "SELECT * FROM Events_ListOfEvents WHERE EventDateTime > '$today' ORDER BY EventDateTime";
    } else {
        
        if (isset($_POST['filterEvents'])) {
            $sqlEventMembers = "SELECT EventId FROM Events_EventMembers WHERE UserId = $userId";
            $sql = "SELECT * FROM Events_ListOfEvents WHERE EventDateTime > '$today' AND EventId IN ($sqlEventMembers) ORDER BY EventDateTime";
        } else {
            
            $sql = "SELECT * FROM Events_ListOfEvents WHERE EventDateTime > '$today' ORDER BY EventDateTime";
        }
    }

    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='col'>";
            echo "<div class='card event-card bg-light'>";
            echo "<div style='margin-top:15px; margin-right: 15px;'>";

            if (in_array($row['EventId'], array_values(getSignedUpEventsByUser($userId, $connection)))) {
                echo "<button class='btn btn-danger btn-save' style='position: flex; float: right; margin-bottom:10px;' data-SingUpEvent-id='{$row["EventId"]}' onclick='toggleSingUpForEvent(this, {$row["EventId"]})'>Wypisz się</button>";
            } else {
                echo "<button class='btn btn-success btn-save' style='position: flex; float: right; margin-bottom:10px;' data-SingUpEvent-id='{$row["EventId"]}' onclick='toggleSingUpForEvent(this, {$row["EventId"]})'>Zapisz się</button>";
            }

            echo "</div>";    
            echo "<p class='card-text text-center'>Lokalizacja: {$row['EventLocation']}</p>";

            if (!empty($row['EventImage'])) {
                $imageData = base64_encode($row['EventImage']);
                $src = "data:image/jpeg;base64," . $imageData;
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

            echo "<p class='card-text'> {$formattedDate}, godzina {$hourAndMinute}</p>";
            echo "<a href='../U_Event/UserEventDetails.php?id={$row['EventId']}' class='btn btn-secondary'>Szczegóły wydarzenia</a>";

            echo "<button class='btn btn-favorite' style='margin-top:0px;' data-event-id='{$row['EventId']}' onclick='toggleFavorite(this, {$row['EventId']})'>";

            if (in_array($row['EventId'], array_values(getFavouriteEventsbyUser($userId, $connection)))) {
                echo '<i class="bi bi-x-circle-fill" style="font-size:30px; align-items: center; justify-content: center;"></i>';
            } else {
                echo '<i class="bi bi-heart-fill" style="font-size:30px; align-items: center; justify-content: center; "></i>';
            }

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

<script 
src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
</script>
<script>
async function toggleFavorite(button, eventId) {
    try {
        var response = await fetch(`toggleFavourite.php?eventId=${eventId}`);
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
        var response = await fetch(`toggleSingUpForEvent.php?eventId=${eventId}`);
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
    } catch (error) {
        console.error('Wystąpił błąd:', error);
    }
}
</script>
