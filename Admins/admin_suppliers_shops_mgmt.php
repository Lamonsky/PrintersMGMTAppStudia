<?php
session_start();
require_once('../Config/db_connection.php');

// Sprawdź, czy użytkownik jest zalogowany jako admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../Users/login.php');
    exit();
}

// Obsługa dodawania lokalizacji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_location'])) {
    $kod_lokalizacji = $_POST['kod_lokalizacji'];
    $nazwa_lokalizacji = $_POST['nazwa_lokalizacji'];
    $ilosc_drukarek = $_POST['ilosc_drukarek'];

    // Wstawienie nowej lokalizacji do tabeli Lokalizacja
    $query_insert_location = "INSERT INTO Lokalizacja (Kod, Nazwa_Lokalizacji, IleDrukarek) 
                              VALUES ('$kod_lokalizacji', '$nazwa_lokalizacji', '$ilosc_drukarek')";
    $result_insert_location = mysqli_query($conn, $query_insert_location);

    if ($result_insert_location) {
        // Lokalizacja została pomyślnie dodana
        // Możesz wyświetlić komunikat potwierdzający
        echo "Lokalizacja została dodana pomyślnie.";
    } else {
        // Wystąpił błąd podczas dodawania lokalizacji
        // Możesz wyświetlić komunikat błędu
        echo "Wystąpił błąd podczas dodawania lokalizacji.";
    }
}

// Obsługa dodawania dostawcy
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_supplier'])) {
    $nazwa_dostawcy = $_POST['nazwa_dostawcy'];
    $mail_dostawcy = $_POST['mail_dostawcy'];

    // Wstawienie nowego dostawcy do tabeli Dostawca
    $query_insert_supplier = "INSERT INTO Dostawca (Nazwa, Mail) 
                              VALUES ('$nazwa_dostawcy', '$mail_dostawcy')";
    $result_insert_supplier = mysqli_query($conn, $query_insert_supplier);

    if ($result_insert_supplier) {
        // Dostawca został pomyślnie dodany
        // Możesz wyświetlić komunikat potwierdzający
        echo "Dostawca został dodany pomyślnie.";
    } else {
        // Wystąpił błąd podczas dodawania dostawcy
        // Możesz wyświetlić komunikat błędu
        echo "Wystąpił błąd podczas dodawania dostawcy.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny - Zarządzanie Lokalizacjami i Dostawcami</title>
    <link rel="stylesheet" href="../Style/styles.css">
</head>
<body>
    <div id="holder">
        <header><a href="admin_panel.php" class="button">Powrót do panelu administracyjnego</a></header>
        <header><h2>Panel Administracyjny - Zarządzanie Lokalizacjami i Dostawcami</h2></header>
        <div id="body">
            <div class="sekcja">
                <h3>Dodaj Lokalizację</h3>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="kod_lokalizacji">Kod Lokalizacji:</label><br>
                    <input type="text" id="kod_lokalizacji" name="kod_lokalizacji" required><br>
                    <label for="nazwa_lokalizacji">Nazwa Lokalizacji:</label><br>
                    <input type="text" id="nazwa_lokalizacji" name="nazwa_lokalizacji" required><br>
                    <label for="ilosc_drukarek">Ilość Drukarek:</label><br>
                    <input type="number" id="ilosc_drukarek" name="ilosc_drukarek" required><br>
                    <input type="submit" name="submit_location" value="Dodaj Lokalizację">
                </form>
                <!-- Wyświetlanie Lokalizacji -->
                <h3>Lista Lokalizacji</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Kod Lokalizacji</th>
                            <th>Nazwa Lokalizacji</th>
                            <th>Ilość Drukarek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Zapytanie do bazy danych o lokalizacje
                            $query_locations = "SELECT * FROM Lokalizacja";
                            $result_locations = mysqli_query($conn, $query_locations);

                            // Iteracja przez wyniki zapytania i wyświetlanie danych w tabeli
                            while ($row_location = mysqli_fetch_assoc($result_locations)) {
                                echo "<tr>";
                                echo "<td>".$row_location['Kod']."</td>";
                                echo "<td>".$row_location['Nazwa_Lokalizacji']."</td>";
                                echo "<td>".$row_location['IleDrukarek']."</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="sekcja">
                <h3>Dodaj Dostawcę</h3>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="nazwa_dostawcy">Nazwa Dostawcy:</label><br>
                    <input type="text" id="nazwa_dostawcy" name="nazwa_dostawcy" required><br>
                    <label for="mail_dostawcy">Mail Dostawcy:</label><br>
                    <input type="email" id="mail_dostawcy" name="mail_dostawcy" required><br>
                    <input type="submit" name="submit_supplier" value="Dodaj Dostawcę">
                </form>
                <!-- Wyświetlanie Dostawców -->
                <h3>Lista Dostawców</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nazwa Dostawcy</th>
                            <th>Mail Dostawcy</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Zapytanie do bazy danych o dostawców
                            $query_suppliers = "SELECT * FROM Dostawca";
                            $result_suppliers = mysqli_query($conn, $query_suppliers);

                            // Iteracja przez wyniki zapytania i wyświetlanie danych w tabeli
                            while ($row_supplier = mysqli_fetch_assoc($result_suppliers)) {
                                echo "<tr>";
                                echo "<td>".$row_supplier['IDDostawcy']."</td>";
                                echo "<td>".$row_supplier['Nazwa']."</td>";
                                echo "<td>".$row_supplier['Mail']."</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
