<div class="container container-form">
    <div class="tabs">
        <a href="#" class="tab" style="margin-right:0; background-color: #979292;">Rejestracja</a>
        <a href="../G_LogIn/GuestLogIn.php" class="tab" style="margin-left:0;">Logowanie</a>
    </div> 
    <form action="Register.php" method="post" style="display: flex; flex-direction: column; align-items: center;">
        <div style="width: 100%; display: flex; justify-content: space-between; margin-top:20px;">
            <div style="width: 48%;">
                <label for="name" style="width:100%;">Imię:</label>
                <input type="text" id="name" name="name" required style="width:100%;">
            </div>
            <div style="width: 48%;">
                <label for="surname" style="width:100%;">Nazwisko:</label>
                <input type="text" id="surname" name="surname" required  style="width:100%;">
            </div>
        </div>
        <div style="width: 100%; display: flex; justify-content: space-between; margin-top: 15px;">
            <div style="width: 32%;">
                <label for="date_of_birth" style="width:100%;">Data urodzenia:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" required style="width:100%;">
            </div>
            <div style="width: 64%; display: flex; align-items: flex-end;">
                    <label style="width:100%;">Płeć:</label>
                    <input type="radio" id="male" name="gender" value="male" required>
                    <label for="male" style="margin-right:20px;">Mężczyzna</label>
                    <input type="radio" id="female" name="gender" value="female" required>
                    <label for="female">Kobieta</label>
            </div>
        </div>
        <div style="width: 100%; margin-top: 15px;">
            <label for="email" style="width:100%;">Adres e-mail:</label>
            <input type="email" id="email" name="email" required style="width:100%;">
        </div>
        <div style="width: 100%;">
            <label for="login" style="width:100%;">Login:</label>
            <input type="text" id="login" name="login" required style="width:100%;">
        </div>
        <div style="width: 100%; display: flex; justify-content: space-between; margin-top: 15px;">
            <div style="width: 48%;">
                <label for="password" style="width:100%;">Hasło:</label>
                <input type="password" id="password" name="password" required style="width:100%;">
            </div>
            <div style="width: 48%;">
                <label for="repeat_password" style="width:100%;">Powtórz hasło:</label>
                <input type="password" id="repeat_password" name="repeat_password" required style="width:100%;">
            </div>
        </div>
        <input type="submit" value="Zarejestruj" style="width:50%; margin-top:40px; height: 50px;">
    </form>
</div>
