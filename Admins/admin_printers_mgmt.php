<?php
session_start();

require_once('../Config/db_connection.php');

// Obsługa dodawania do tabeli DrukarkiModele
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_drukarki_modele'])) {
    $producent_modele = $_POST['producent_modele'];
    $model_modele = $_POST['model_modele'];

    // Zabezpieczenie przed atakami SQL injection
    $producent_modele = mysqli_real_escape_string($conn, $producent_modele);
    $model_modele = mysqli_real_escape_string($conn, $model_modele);

    // Dodanie danych do tabeli DrukarkiModele
    $query_drukarki_modele = "INSERT INTO DrukarkiModele (Producent, Model) VALUES ('$producent_modele', '$model_modele')";
    $result_drukarki_modele = mysqli_query($conn, $query_drukarki_modele);

    if ($result_drukarki_modele) {
        echo "Dane zostały dodane do tabeli DrukarkiModele.";
    } else {
        echo "Błąd podczas dodawania danych do tabeli DrukarkiModele: " . mysqli_error($conn);
    }
}

// Obsługa dodawania do tabeli DrukarkiInwentaryzacja
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_drukarki_inwentaryzacja'])) {
    $model_inwentaryzacja = $_POST['model_inwentaryzacja'];
    $numer_seryjny = $_POST['numer_seryjny'];
    $adres_ip = $_POST['adres_ip'];
    $lokalizacja = $_POST['lokalizacja'];
    $iddostawcy_inwentaryzacja = $_POST['iddostawcy_inwentaryzacja']; // Pobierz ID dostawcy ze zmiennych sesji lub innego źródła
    $lokalizacja_dzial = $_POST['lokalizacja_dzial'];
    // Zabezpieczenie przed atakami SQL injection
    $numer_seryjny = mysqli_real_escape_string($conn, $numer_seryjny);
    $adres_ip = mysqli_real_escape_string($conn, $adres_ip);
    $lokalizacja = mysqli_real_escape_string($conn, $lokalizacja);

    // Dodanie danych do tabeli DrukarkiInwentaryzacja
    $query_drukarki_inwentaryzacja = "INSERT INTO DrukarkiInwentaryzacja (IDDostawcy, IDModeluDrukarki, IDLokalizacji, NumerSeryjny, AdresIP, Lokalizacja) VALUES ('$iddostawcy_inwentaryzacja', '$model_inwentaryzacja', '$lokalizacja', '$numer_seryjny', '$adres_ip', '$lokalizacja_dzial')";
    $result_drukarki_inwentaryzacja = mysqli_query($conn, $query_drukarki_inwentaryzacja);

    if ($result_drukarki_inwentaryzacja) {
        echo "Dane zostały dodane do tabeli DrukarkiInwentaryzacja.";
    } else {
        echo "Błąd podczas dodawania danych do tabeli DrukarkiInwentaryzacja: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny - Zarządzanie Drukarkami</title>
    <link rel="stylesheet" href="../Style/styles.css">
</head>
<body>
<div id="holder">
    <header><a href="admin_panel.php" class="button">Powrót do panelu administracyjnego</a></header>
    <header><h2>Panel Administracyjny - Zarządzanie Drukarkami</h2></header>
    <div id="body">
        <div class="sekcja">
            <h3>Drukarki Modele</h3>
            <div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="producent_modele">Producent:</label><br>
                    <input type="text" id="producent_modele" name="producent_modele" required><br>
                    <label for="model_modele">Model:</label><br>
                    <input type="text" id="model_modele" name="model_modele" required><br>
                    <input type="submit" name="submit_drukarki_modele" value="Dodaj">
                </form>
            </div>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producent</th>
                            <th>Model</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Zapytanie do bazy danych o modele drukarek
                            $query_modele = "SELECT * FROM DrukarkiModele";
                            $result_modele = mysqli_query($conn, $query_modele);

                            // Iteracja przez wyniki zapytania i wyświetlanie danych w tabeli
                            while ($row_modele = mysqli_fetch_assoc($result_modele)) {
                                echo "<tr>";
                                echo "<td>".$row_modele['IDdrukarki']."</td>";
                                echo "<td>".$row_modele['Producent']."</td>";
                                echo "<td>".$row_modele['Model']."</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sekcja">
            <h3>Drukarki Inwentaryzacja</h3>
            <div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="model_inwentaryzacja">Model:</label><br>
                    <select id="model_inwentaryzacja" name="model_inwentaryzacja">
                        <?php
                            // Zapytanie do bazy danych o modele drukarek
                            $query_modele = "SELECT * FROM DrukarkiModele";
                            $result_modele = mysqli_query($conn, $query_modele);

                            // Iteracja przez wyniki zapytania i wyświetlanie opcji w comboboxie
                            while ($row_modele = mysqli_fetch_assoc($result_modele)) {
                                echo "<option value='".$row_modele['IDdrukarki']."'>".$row_modele['Producent']." - ".$row_modele['Model']."</option>";
                            }
                        ?>
                    </select><br>
                    <label for="numer_seryjny">Numer Seryjny:</label><br>
                    <input type="text" id="numer_seryjny" name="numer_seryjny" required><br>
                    <label for="adres_ip">Adres IP:</label><br>
                    <input type="text" id="adres_ip" name="adres_ip" required><br>
                    <label for="lokalizacja">Lokalizacja:</label><br>
                    <select id="lokalizacja" name="lokalizacja">
                        <?php
                            // Zapytanie do bazy danych o lokalizacje
                            $query_lokalizacja = "SELECT * FROM Lokalizacja";
                            $result_lokalizacja = mysqli_query($conn, $query_lokalizacja);

                            // Iteracja przez wyniki zapytania i wyświetlanie opcji w comboboxie
                            while ($row_lokalizacja = mysqli_fetch_assoc($result_lokalizacja)) {
                                echo "<option value='".$row_lokalizacja['IDLokalizacji']."'>".$row_lokalizacja['Kod']." - ".$row_lokalizacja['Nazwa_Lokalizacji']."</option>";
                            }
                        ?>
                    </select><br>
                    <label for="lokalizacja_dzial">Dział:</label><br>
                    <input type="text" id="lokalizacja_dzial" name="lokalizacja_dzial"><br>
                    <label for="iddostawcy_inwentaryzacja">Dostawca:</label><br>
                    <select id="iddostawcy_inwentaryzacja" name="iddostawcy_inwentaryzacja">
                        <?php
                            // Zapytanie do bazy danych o dostawców
                            $query_dostawcy = "SELECT * FROM Dostawca";
                            $result_dostawcy = mysqli_query($conn, $query_dostawcy);

                            // Iteracja przez wyniki zapytania i wyświetlanie opcji w comboboxie
                            while ($row_dostawcy = mysqli_fetch_assoc($result_dostawcy)) {
                                echo "<option value='".$row_dostawcy['IDDostawcy']."'>".$row_dostawcy['Nazwa']."</option>";
                            }
                        ?>
                    </select><br>
                    <input type="submit" name="submit_drukarki_inwentaryzacja" value="Dodaj">
                </form>
            </div>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producent</th>
                            <th>Model</th>
                            <th>Dostawca</th>
                            <th>Numer Seryjny</th>
                            <th>Adres IP</th>
                            <th>Lokalizacja</th>
                            <th>Dział</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Zapytanie do bazy danych o drukarki inwentaryzacja
                            $query_inwentaryzacja = "SELECT di.IDdrukarki, di.Lokalizacja, dm.Producent, dm.Model, di.IDDostawcy, di.NumerSeryjny, di.AdresIP, l.Kod, l.Nazwa_Lokalizacji, d.Nazwa FROM DrukarkiInwentaryzacja di INNER JOIN Dostawca d ON di.IDDostawcy = d.IDDostawcy INNER JOIN DrukarkiModele dm ON di.IDModeluDrukarki = dm.IDdrukarki INNER JOIN Lokalizacja l ON di.IDLokalizacji = l.IDLokalizacji";
                            $result_inwentaryzacja = mysqli_query($conn, $query_inwentaryzacja);

                            // Iteracja przez wyniki zapytania i wyświetlanie danych w tabeli
                            while ($row_inwentaryzacja = mysqli_fetch_assoc($result_inwentaryzacja)) {
                                echo "<tr>";
                                echo "<td>".$row_inwentaryzacja['IDdrukarki']."</td>";
                                echo "<td>".$row_inwentaryzacja['Producent']."</td>";
                                echo "<td>".$row_inwentaryzacja['Model']."</td>";
                                echo "<td>".$row_inwentaryzacja['Nazwa']."</td>";
                                echo "<td>".$row_inwentaryzacja['NumerSeryjny']."</td>";
                                echo "<td>".$row_inwentaryzacja['AdresIP']."</td>";
                                echo "<td>".$row_inwentaryzacja['Kod']." - ".$row_inwentaryzacja['Nazwa_Lokalizacji']."</td>";
                                echo "<td>".$row_inwentaryzacja['Lokalizacja']."</td>";
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
