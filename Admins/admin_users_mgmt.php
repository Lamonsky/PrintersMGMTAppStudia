<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany jako admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../Users/login.php');
    exit();
}

// Importuj plik z połączeniem z bazą danych
require_once '../Config/db_connection.php';

// Obsługa dodawania użytkownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $login = $_POST['login'];
    $password = md5($_POST['password']); // Konwertuj hasło na MD5
    $rola = $_POST['rola'];

    // Zabezpieczenie przed atakami SQL injection
    $imie = mysqli_real_escape_string($conn, $imie);
    $nazwisko = mysqli_real_escape_string($conn, $nazwisko);
    $login = mysqli_real_escape_string($conn, $login);
    $rola = mysqli_real_escape_string($conn, $rola);

    // Sprawdź, czy istnieje już użytkownik o podanym loginie
    $check_query = "SELECT * FROM Uzytkownicy WHERE Login='$login'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Użytkownik o podanym loginie już istnieje
        // Wyświetl komunikat o błędzie lub podejmij odpowiednie działania
        echo "Użytkownik o podanym loginie już istnieje!";
    } else {
        // Dodaj użytkownika do bazy danych
        $insert_query = "INSERT INTO Uzytkownicy (Imie, Nazwisko, Login, Haslo, Rola) 
                         VALUES ('$imie', '$nazwisko', '$login', '$password', '$rola')";
        $insert_result = mysqli_query($conn, $insert_query);

        if ($insert_result) {
            // Użytkownik został dodany pomyślnie
            // Możesz wyświetlić komunikat potwierdzający
        } else {
            // Wystąpił błąd podczas dodawania użytkownika
            // Możesz wyświetlić komunikat błędu
            echo "Wystąpił błąd podczas dodawania użytkownika!";
        }
    }
}

// Obsługa usuwania użytkownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Zabezpieczenie przed atakami SQL injection
    $user_id = mysqli_real_escape_string($conn, $user_id);

    // Usuń użytkownika z bazy danych
    $delete_query = "DELETE FROM Uzytkownicy WHERE UzytkownikID='$user_id'";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        // Użytkownik został usunięty pomyślnie
        // Możesz wyświetlić komunikat potwierdzający
    } else {
        // Wystąpił błąd podczas usuwania użytkownika
        // Możesz wyświetlić komunikat błędu
    }
}

// Obsługa edycji użytkownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $new_imie = $_POST['new_imie'];
    $new_nazwisko = $_POST['new_nazwisko'];
    $new_login = $_POST['new_login'];
    $new_password = md5($_POST['new_password']); // Konwertuj nowe hasło na MD5
    $new_rola = $_POST['new_role'];

    // Zabezpieczenie przed atakami SQL injection
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $new_imie = mysqli_real_escape_string($conn, $new_imie);
    $new_nazwisko = mysqli_real_escape_string($conn, $new_nazwisko);
    $new_login = mysqli_real_escape_string($conn, $new_login);
    $new_rola = mysqli_real_escape_string($conn, $new_rola);

    // Zaktualizuj dane użytkownika w bazie danych
    $update_query = "UPDATE Uzytkownicy SET 
                     Imie='$new_imie', 
                     Nazwisko='$new_nazwisko', 
                     Login='$new_login', 
                     Haslo='$new_password', 
                     Rola='$new_rola' 
                     WHERE UzytkownikID='$user_id'";
    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
        // Dane użytkownika zostały zaktualizowane pomyślnie
        // Możesz wyświetlić komunikat potwierdzający
    } else {
        // Wystąpił błąd podczas aktualizacji danych użytkownika
        // Możesz wyświetlić komunikat błędu
    }
}

// Pobierz wszystkich użytkowników z bazy danych
$query_users = "SELECT * FROM Uzytkownicy";
$result_users = mysqli_query($conn, $query_users);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <link rel="stylesheet" href="../Style/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie użytkownikami</title>
</head>
<body>
<div id="holder">
    <header><a href="admin_panel.php" class="button">Powrót do panelu administracyjnego</a></header>
    <header><h2>Zarządzanie użytkownikami</h2></header>
    <div id="body">
        <div class="sekcja">
            <!-- Formularz dla dodawania użytkownika w układzie poziomym -->
            <h3>Dodaj użytkownika</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="imie">Imię:</label>
                    <input type="text" id="imie" name="imie" required>
                </div>
                <div class="form-group">
                    <label for="nazwisko">Nazwisko:</label>
                    <input type="text" id="nazwisko" name="nazwisko" required>
                </div>
                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" name="login" required>
                </div>
                <div class="form-group">
                    <label for="password">Hasło:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="rola">Rola:</label>
                    <select id="rola" name="rola">
                        <option value="Admin">Admin</option>
                        <option value="Uzytkownik">Użytkownik</option>
                    </select>
                </div>
                <input type="submit" name="add_user" value="Dodaj użytkownika">
            </form>
        </div>


        <div class="sekcja">
        <!-- Tabela z użytkownikami -->
            <h3>Użytkownicy</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Imię</th>
                    <th>Nowe imię</th>
                    <th>Nazwisko</th>
                    <th>Nowe nazwisko</th>
                    <th>Login</th>
                    <th>Nowy login</th>
                    <th>Nowe hasło</th>
                    <th>Rola</th>
                    <th>Nowa rola</th>
                    <th>Edycja</th>
                    <th>Usuwanie</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result_users)): ?>
                    <tr>
                        <td><?php echo $row['UzytkownikID']; ?></td>
                        <td><?php echo $row['Imie']; ?></td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <td>
                            <!-- Formularz dla zmiany imienia -->
                                <input type="hidden" name="user_id" value="<?php echo $row['UzytkownikID']; ?>">
                                <input type="text" name="new_imie" value="<?php echo $row['Imie']; ?>" required>
                        </td>
                        <td><?php echo $row['Nazwisko']; ?></td>
                        <td>
                            <!-- Formularz dla zmiany nazwiska -->
                                <input type="hidden" name="user_id" value="<?php echo $row['UzytkownikID']; ?>">
                                <input type="text" name="new_nazwisko" value="<?php echo $row['Nazwisko']; ?>" required>
                        </td>
                        <td><?php echo $row['Login']; ?></td>
                        <td>
                            <!-- Formularz dla zmiany loginu -->
                                <input type="hidden" name="user_id" value="<?php echo $row['UzytkownikID']; ?>">
                                <input type="text" name="new_login" value="<?php echo $row['Login']; ?>" required>
                        </td>
                        <td>
                            <!-- Formularz dla zmiany hasła -->
                                <input type="hidden" name="user_id" value="<?php echo $row['UzytkownikID']; ?>">
                                <input type="password" name="new_password">
                        </td>
                        <td><?php echo $row['Rola']; ?></td>
                        <td>
                            <!-- Formularz dla zmiany roli -->
                                <input type="hidden" name="user_id" value="<?php echo $row['UzytkownikID']; ?>">
                                <select name="new_role">
                                    <option value="Admin" <?php if ($row['Rola'] === 'Admin') echo 'selected'; ?>>Admin</option>
                                    <option value="Uzytkownik" <?php if ($row['Rola'] === 'Uzytkownik') echo 'selected'; ?>>Użytkownik</option>
                                </select>
                        </td>
                        <td>
                            <!-- Przycisk do zatwierdzenia zmian -->
                                <input type="hidden" name="user_id" value="<?php echo $row['UzytkownikID']; ?>">
                                <input type="submit" name="edit_user" value="Zapisz zmiany">
                        </td>
                        <td>
                            <!-- Formularz dla usuwania użytkownika -->
                                <input type="hidden" name="user_id" value="<?php echo $row['UzytkownikID']; ?>">
                                <input type="submit" name="delete_user" value="Usuń">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
