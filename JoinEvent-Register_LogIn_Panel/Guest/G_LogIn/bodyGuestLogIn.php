<div class="container container-form">
    <div class="tabs">
        <a href="../G_Register/GuestRegister.php" class="tab" style="margin-right:0;">Rejestracja</a>
        <a href="#" class="tab" style="margin-left:0;  background-color: #979292;">Logowanie</a>
    </div> 
    <form action="LogIn.php" method="post" style="display: flex; flex-direction: column; align-items: center;">
        <div style="width: 100%; margin-top:50px;">
            <label for="email" style="width:100%;  margin-left:10%;">Login/Adres e-mail:</label>
            <input type="text" id="email" name="email" required style="width:80%; margin-left:10%;">
        </div>
        <div style="width: 100%;">
            <label for="password" style="width:100%;  margin-left:10%;">Hasło:</label>
            <input type="password" id="password" name="password" required style="width:80%;  margin-left:10%;">
        </div>
        <input type="submit" value="Zaloguj" style="width:50%; margin-top:50px; height: 50px;">
    </form>
</div>
