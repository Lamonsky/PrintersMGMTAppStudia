<?php
session_start();

require_once('../Config/db_connection.php');

// Funkcja walidująca dane
function validateInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_faktura'])) {
    $numer_faktury = $_POST['numer_faktury'];

    // Zabezpieczenie przed atakami SQL injection
    $numer_faktury = mysqli_real_escape_string($conn, $numer_faktury);

    // Dodanie danych do tabeli Tonery
    $query_faktura = "INSERT INTO Faktura (numer_faktury) VALUES ('$numer_faktury')";
    $result_faktura = mysqli_query($conn, $query_faktura);

    if ($result_faktura) {
        echo "Dane zostały dodane do tabeli Faktura.";
    } else {
        echo "Błąd podczas dodawania danych do tabeli Faktura: " . mysqli_error($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_faktura'])) {
    $idfaktury = $_POST['idfaktura'];

    // Zabezpieczenie przed atakami SQL injection
    $idfaktury = mysqli_real_escape_string($conn, $idfaktury);

    // Usuń użytkownika z bazy danych
    $delete_query = "DELETE FROM Faktura WHERE id='$idfaktury'";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        echo "Rekord został prawidłowo usunięty<br>";
    } else {
        echo "Błąd podczas usuwania rekordu z tabeli Dzierzawa: " . mysqli_error($conn). "<br>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_faktura'])) {
    $numer_faktury = $_POST['numer_faktury'];
    $idfaktury = $_POST['idfaktura'];
    // Zabezpieczenie przed atakami SQL injection
    $numer_faktury = mysqli_real_escape_string($conn, $numer_faktury);

    // Dodanie danych do tabeli Tonery
    $query_faktura = "UPDATE Faktura SET
                        numer_faktury='$numer_faktury'
                        WHERE id='$idfaktury'
                      ";

    $result_faktura = mysqli_query($conn, $query_faktura);

    if ($result_faktura) {
        echo "Dane zostały zaktualizowane w tabeli Faktura.";
    } else {
        echo "Błąd podczas aktualizacji danych w tabeli Faktura: " . mysqli_error($conn);
    }
}

$query_faktura_select = "SELECT 
                        id, numer_faktury
                        FROM Faktura";
$result_faktura_select = mysqli_query($conn, $query_faktura_select);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <link rel="stylesheet" href="../Style/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie dzierżawami</title>
</head>
<body>
<div id="holder">
    <header><a href="admin_panel.php" class="button">Powrót do panelu administracyjnego</a></header>
    <header><h2>Naprawy</h2></header>
    <div id="body">
        <div class="sekcja">
            <h3>Faktura</h3>
            <div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="numer_faktury">Numer faktury:</label><br>
                    <input type="text" id="numer_faktury" name="numer_faktury" required><br>
                    <input type="submit" name="submit_faktura" value="Dodaj">
                </form>
            </div>
        </div>
        <div class="sekcja">
            <h3>Dzierżawa</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Faktura</th>
                    <th>Nowa Faktura</th>
                    <th>Edycja</th>
                    <th>Usuwanie</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result_faktura_select)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <td><?php echo $row['numer_faktury']; ?></td>
                            <td>
                                <input type="hidden" name="idfaktura" value="<?php echo $row['id']; ?>">
                                <input type="text" name="numer_faktury" value="<?php echo $row['numer_faktury']; ?>" required>
                        </td>
                        <td>
                            <!-- Przycisk do zatwierdzenia zmian -->
                                <input type="hidden" name="idfaktura" value="<?php echo $row['id']; ?>">
                                <input type="submit" name="edit_faktura" value="Zapisz zmiany">
                        </td>
                        <td>
                            <!-- Formularz dla usuwania użytkownika -->
                                <input type="hidden" name="idfaktura" value="<?php echo $row['id']; ?>">
                                <input type="submit" name="delete_faktura" value="Usuń">
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
