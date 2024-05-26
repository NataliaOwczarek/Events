<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>D&B&N Events</title>
    <link rel="icon" href="../../assets/logo.png" type="image/png">


    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@latest/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.17.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Dodatkowe style CSS dla Twojej aplikacji -->
    <link rel="stylesheet" href="../../css/styles.css">
    <script>
        var leavingPage = true;

        window.addEventListener('beforeunload', function (e) {
            if (leavingPage) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '.../Guest/G_Login/LogOut.php', false);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('action=logout');
                console.log(xhr.responseText);
            }
        });

        window.addEventListener('unload', function (e) {
            if (leavingPage) {
                var xhr = new XMLHttpRequest();ś
                xhr.open('POST', '.../Guest/G_Login/LogOut.php', false);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('action=logout');
                console.log(xhr.responseText);
            }
        });

        // Ustawienie leavingPage na false, gdy użytkownik odświeża stronę
        window.addEventListener('beforeunload', function () {
            leavingPage = false;
        });
    </script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-pzsj9XoR8I4eE8SCDkV9SGCKsJUvj4d1T7rhBPdbjBCqb7Bp4gLs8+R8O+t26KXNShN0DH+CS68vOMOTwNkgCmQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
