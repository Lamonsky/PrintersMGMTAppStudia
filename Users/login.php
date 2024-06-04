<?php
// login.php

session_start();
require_once '../Config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Zabezpieczenie przed atakami SQL injection
    $login = mysqli_real_escape_string($conn, $login);

    // Pobierz hasło z bazy danych dla danego loginu
    $query = "SELECT UzytkownikID, Haslo, Rola FROM Uzytkownicy WHERE Login='$login'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row['Haslo'];
        $role = $row['Rola'];

        // Sprawdź czy hasło jest poprawne
        if (md5($password) === $storedPassword) {
            $_SESSION['login'] = true;
            $_SESSION['role'] = $role;
            $_SESSION['user_id'] = $row['UzytkownikID']; // Ustaw ID użytkownika w sesji

            if ($role === 'Admin') {
                header('Location: ../Admins/admin_panel.php');
            } else {
                header('Location: ../Users/user_panel.php');
            }
            exit();
        } else {
            $error = "Niepoprawne hasło.";
        }
    } else {
        $error = "Użytkownik o podanym loginie nie istnieje.";
    }
}
?>


<!DOCTYPE html>
<html lang="pl">
<head>
    <link rel="stylesheet" href="../Style/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
</head>
<body>
    <div class="logowanie">
        <h2>Logowanie</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="login">Login:</label><br>
            <input type="text" id="login" name="login" required><br>
            <label for="password">Hasło:</label><br>
            <input type="password" id="password" name="password" required><br><br>
            <input type="submit" value="Zaloguj">
        </form>
    </div>
</body>
</html>
