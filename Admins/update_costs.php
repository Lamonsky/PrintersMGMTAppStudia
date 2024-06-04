<?php
session_start();
require_once('../Config/db_connection.php');

// Sprawdź, czy użytkownik jest zalogowany jako admin
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Users/login.php');
    exit();
}

if ($_SESSION['role'] == 'Admin') {
    $panel = "../Admins/admin_panel.php";
}

if ($_SESSION['role'] == 'Uzytkownik') {
    $panel = "../Users/user_panel.php";
}

// Funkcja zwracająca pola formularza na podstawie wybranej tabeli
function getFormFields($table) {
    switch($table) {
        case 'Dzierzawa':
            return [
                'id' => 'ID',
                'idlokalizacji' => 'ID Lokalizacji',
                'kwotaJedNetto' => 'Kwota Jednostkowa Netto',
                'ilosc' => 'Ilość'
                // Dodaj inne pola dla tabeli Dzierzawa
            ];
        case 'Naprawy':
            return [
                'id' => 'ID',
                'idlokalizacji' => 'ID Lokalizacji',
                'kosztNaprawy' => 'Koszt Naprawy'
                // Dodaj inne pola dla tabeli Naprawy
            ];
        case 'Tonery':
            return [
                'id' => 'ID',
                'idlokalizacji' => 'ID Lokalizacji',
                'nazwaTonera' => 'Nazwa Tonera',
                'ilosc' => 'Ilość'
                // Dodaj inne pola dla tabeli Tonery
            ];
        case 'Faktury':
            return [
                'id' => 'ID',
                'idlokalizacji' => 'ID Lokalizacji',
                'kwotaFaktury' => 'Kwota Faktury',
                'dataWystawienia' => 'Data Wystawienia'
                // Dodaj inne pola dla tabeli Faktury
            ];
        default:
            return [];
    }
}

$table = isset($_GET['table']) ? $_GET['table'] : 'Dzierzawa'; // Domyślnie Dzierzawa
$fields = getFormFields($table);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pobierz dane z formularza i zaktualizuj rekord w odpowiedniej tabeli
    $id = $_POST['id'];
    $updates = [];

    foreach ($fields as $field => $label) {
        if ($field !== 'id') {
            $updates[] = "$field='" . $_POST[$field] . "'";
        }
    }

    $updates_string = implode(', ', $updates);
    $sql = "UPDATE $table SET $updates_string WHERE ID='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Rekord został zaktualizowany pomyślnie w tabeli $table";
    } else {
        echo "Błąd: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktualizuj Rekord</title>
    <link rel="stylesheet" href="../Style/styles.css">
</head>
<body>
<div id="holder">
    <header>
        <a href="<?php echo $panel ?>" class="button">Powrót do panelu administracyjnego</a>
    </header>
    <header>
        <h2>Aktualizuj Rekord</h2>
    </header>
    <div id="body">
        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="table">Wybierz tabelę:</label>
            <select id="table" name="table" onchange="this.form.submit()">
                <option value="Dzierzawa" <?php if ($table == 'Dzierzawa') echo 'selected'; ?>>Dzierzawa</option>
                <option value="Naprawy" <?php if ($table == 'Naprawy') echo 'selected'; ?>>Naprawy</option>
                <option value="Tonery" <?php if ($table == 'Tonery') echo 'selected'; ?>>Tonery</option>
                <option value="Faktury" <?php if ($table == 'Faktury') echo 'selected'; ?>>Faktury</option>
            </select>
        </form>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?table=' . $table; ?>">
            <?php foreach ($fields as $field => $label): ?>
                <label for="<?php echo $field; ?>"><?php echo $label; ?>:</label>
                <input type="text" id="<?php echo $field; ?>" name="<?php echo $field; ?>" required>
            <?php endforeach; ?>
            <input type="submit" value="Aktualizuj">
        </form>
    </div>
</div>
</body>
</html>
