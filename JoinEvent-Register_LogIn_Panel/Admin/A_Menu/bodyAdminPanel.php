<head>
    <style>
        .container {
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
        }

        th,
        td {
            border: 1px solid #4d4c4c;
            text-align: center;
            padding: 5px;
        }

        th {
            background: #4d4c4c !important;
            color: white !important;
        }

        .fa {
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .rotate {
            transform: rotate(90deg);
        }

        canvas {
            display: block;
            margin: 20px auto;
        }

        .chart-container {
            width: 80%;
            margin: 20px auto;
        }

        .card-body table {
            width: 100% !important;
        }

        .card-body-table {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60vh;
        }
        .btn-secondary{
            background: #4d4c4c !important;
            color: white !important;
        }
        .profiles-table {
            width: 100%;
        }
    </style>
    <meta http-equiv="refresh" content="30">
</head>
<?php
require_once "../../config/config.php";
$connection = mysqli_connect($host, $user, $password, $dbname);

if ($connection->connect_error) {
    die("Błąd połączenia: " . $connection->connect_error);
}

$sqlGender = "SELECT Sex, COUNT(*) as GenderCount FROM Events_Users u INNER JOIN Events_Accounts a ON u.UserId=a.UserId WHERE a.IsActive=1 GROUP BY Sex";
$resultGender = $connection->query($sqlGender);

$genderData = [];
while ($rowGender = $resultGender->fetch_assoc()) {
    $genderData[$rowGender['Sex']] = $rowGender['GenderCount'];
}

$sqlUsers = "SELECT 
            U.UserId,
            U.Name,
            U.Surname,
            U.Sex,
            U.Date_of_birth,
            A.IsAdmin,
            A.Email,
            A.Login,
            A.Password,
            A.IsActive,
            A.Photo
        FROM
            Events_Users U
        INNER JOIN
            Events_Accounts A ON U.UserId = A.UserId
        WHERE 
            A.IsActive=1";

$resultUsers = $connection->query($sqlUsers);

$sqlAverageAge = "SELECT AVG(YEAR(CURDATE()) - YEAR(Date_of_birth)) AS AverageAge FROM Events_Users u
INNER JOIN Events_Accounts a ON u.UserId=a.UserId WHERE a.IsActive=1";
$resultAverageAge = $connection->query($sqlAverageAge);

$averageAge = 0;
if ($rowAverageAge = $resultAverageAge->fetch_assoc()) {
    $averageAge = round($rowAverageAge['AverageAge'], 2);
}

$sqlLoggedInUsers = "SELECT COUNT(*) as LoggedInUsers FROM Events_Logs WHERE LogOutDateTime is Null";
$resultLoggedInUsers = $connection->query($sqlLoggedInUsers);

$loggedInUsers = 0;
if ($rowLoggedInUsers = $resultLoggedInUsers->fetch_assoc()) {
    $loggedInUsers = $rowLoggedInUsers['LoggedInUsers'];
}

$sqlLogTime = "SELECT ROUND(AVG(TIMESTAMPDIFF(minute, LogInDateTime, LogOutDateTime)), 2) as LogTime 
FROM Events_Logs 
WHERE LogOutDateTime IS NOT NULL;
";
$resultLogTime = $connection->query($sqlLogTime);

$AverageTime = 0;
if ($rowLogTime = $resultLogTime->fetch_assoc()) {
    $AverageTime = ($rowLogTime['LogTime']);
}

$nextWeekEvents = "SELECT COUNT(*) as NextWeekEvents 
                   FROM Events_ListOfEvents 
                   WHERE EventDateTime BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
$resultNextWeekEvents = $connection->query($nextWeekEvents);

$nextWeekEventsCount = 0;
if ($rowNextWeekEvents = $resultNextWeekEvents->fetch_assoc()) {
    $nextWeekEventsCount = $rowNextWeekEvents['NextWeekEvents'];
}
$thisWeekEvents = "SELECT COUNT(*) as ThisWeekEvents 
                   FROM Events_ListOfEvents
                   WHERE DATE(EventCreationDate) BETWEEN CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) + 6 DAY AND CURDATE()";
$resultThisWeekEvents = $connection->query($thisWeekEvents);

$thisWeekEventsCount = 0;
if ($rowThisWeekEvents = $resultThisWeekEvents->fetch_assoc()) {
    $thisWeekEventsCount = $rowThisWeekEvents['ThisWeekEvents'];
}

$connection->close();


?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <canvas id="genderChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Statystyki</th>
                                <th>Liczba</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Liczba użytkowników</td>
                                <td>
                                    <?php echo $resultUsers->num_rows; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Przeciętny wiek użytkownika</td>
                                <td>
                                    <?php echo $averageAge; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Liczba użytkowników zalogowanych</td>
                                <td>
                                    <?php echo $loggedInUsers; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Średni czas spędzony na stronie</td>
                                <td>
                                    <?php echo $AverageTime . ' min'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Liczba wydarzeń zaplanowanych na najbliższy tydzień</td>
                                <td>
                                    <?php echo $nextWeekEventsCount; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Liczba wydarzeń stworzonych w tym tygodniu</td>
                                <td>
                                    <?php echo $thisWeekEventsCount; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body-table">
            <div>
                <h3>Tabela użytkowników</h3>
                <table class="profiles-table">
                    <thead>
                        <tr>
                            <th>Imię</th>
                            <th>Nazwisko</th>
                            <th>Płeć</th>
                            <th>Data urodzenia</th>
                            <th>Email</th>
                            <th>Login</th>
                            <th>Profil</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rowUser = $resultUsers->fetch_assoc()) { ?>
                            <tr>
                                <td>
                                    <?php echo $rowUser['Name']; ?>
                                </td>
                                <td>
                                    <?php echo $rowUser['Surname']; ?>
                                </td>
                                <td>
                                    <?php echo $rowUser['Sex']; ?>
                                </td>
                                <td>
                                    <?php echo $rowUser['Date_of_birth']; ?>
                                </td>
                                <td>
                                    <?php echo $rowUser['Email']; ?>
                                </td>
                                <td>
                                    <?php echo $rowUser['Login']; ?>
                                </td>
                                <td>
                                    <a href="UserProfile.php?user_id=<?php echo $rowUser['UserId']; ?>&action=none"
                                        class="btn btn-secondary">Edytuj profil</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
    var genderData = <?php echo json_encode($genderData); ?>;
    var ctxGender = document.getElementById('genderChart').getContext('2d');
    var total = Object.values(genderData).reduce((a, b) => a + b, 0);
    var genderChart = new Chart(ctxGender, {
        type: 'pie',
        data: {
            labels: Object.keys(genderData),
            datasets: [{
                data: Object.values(genderData),
                backgroundColor: ['#4d4c4c', '#7f8c8d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: true,
                position: 'bottom'
            },
            title: {
                display: true,
                text: 'Podział płci użytkowników'
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var total = dataset.data.reduce(function (previousValue, currentValue, currentIndex, array) {
                            return previousValue + currentValue;
                        });
                        var currentValue = dataset.data[tooltipItem.index];
                        var percentage = ((currentValue / total) * 100).toFixed(2);
                        return data.labels[tooltipItem.index] + ': ' + percentage + '%';
                    }
                }
            }
        }
    });

    function toggleUserProfile(button) {
        var arrowIcon = button.querySelector('.fa');
        if (arrowIcon.classList.contains('rotate')) {
            arrowIcon.classList.remove('rotate');
        } else {
            arrowIcon.classList.add('rotate');
        }
    }

    function toggleAdminStatus(button) {
        button.style.backgroundColor = button.style.backgroundColor === 'red' ? '' : 'red';
    }

    function toggleActiveStatus(button) {
        button.style.backgroundColor = button.style.backgroundColor === 'green' ? '' : 'green';
    }
</script>


</div>