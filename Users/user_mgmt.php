<?php
require_once('../Config/db_connection.php'); // Połączenie z bazą danych

session_start();

// Pobieranie ID użytkownika, który edytuje swoje dane
$user_id = $_SESSION['user_id']; // Załóżmy, że ID użytkownika jest przechowywane w sesji

// Pobranie informacji o użytkowniku z bazy danych
$query = "SELECT * FROM uzytkownicy WHERE UzytkownikID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imie = $row['Imie'];
    $nazwisko = $row['Nazwisko'];
    $login = $row['Login'];
    $haslo = $row['Haslo'];
    $rola = $row['Rola'];
} else {
    echo "Brak danych dla użytkownika o ID: $user_id";
    exit();
}

// Jeśli formularz został przesłany, zaktualizuj informacje o użytkowniku
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $login = $_POST['login'];
    $haslo = $_POST['haslo'];
    
    // Aktualizacja informacji o użytkowniku w bazie danych
    $update_query = "UPDATE uzytkownicy SET Imie=?, Nazwisko=?, Login=?, Haslo=? WHERE UzytkownikID=?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("ssssi", $imie, $nazwisko, $login, $haslo, $user_id);
    if ($stmt_update->execute()) {
        echo "Informacje użytkownika zostały zaktualizowane pomyślnie.";
    } else {
        echo "Błąd podczas aktualizacji informacji użytkownika: " . $stmt_update->error;
    }
    $stmt_update->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../Style/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edycja informacji użytkownika</title>
</head>
<body>
    <header><a href="user_panel.php" class="button">Powrót do panelu użytkownika</a></header>
    <header><h2>Edycja informacji użytkownika</h2></header>
    <div id="body">
        <div class="sekcja">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="imie">Imię:</label>
                <input type="text" id="imie" name="imie" value="<?php echo $imie; ?>"><br><br>
                <label for="nazwisko">Nazwisko:</label>
                <input type="text" id="nazwisko" name="nazwisko" value="<?php echo $nazwisko; ?>"><br><br>
                <label for="login">Login:</label>
                <input type="text" id="login" name="login" value="<?php echo $login; ?>"><br><br>
                <label for="haslo">Hasło:</label>
                <input type="password" id="haslo" name="haslo" value="<?php echo $haslo; ?>"><br><br>
                <input type="submit" value="Zapisz zmiany">
            </form>
        </div>
    </div>
</body>
</html>
