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

// Obsługa dodawania danych do tabeli Dzierzawa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_dzierzawa'])) {
    $iddrukarki_dzierzawa = $_POST['iddrukarki_dzierzawa'];
    $iddostawcy_dzierzawa = $_POST['iddostawcy_dzierzawa'];
    $kwota_jed_netto = isset($_POST['kwota_jed_netto']) ? floatval(str_replace(',', '.', validateInput($_POST['kwota_jed_netto']))) : 0;
    $ilosc_dzierzawa = isset($_POST['ilosc_dzierzawa']) ? intval($_POST['ilosc_dzierzawa']) : 0;
    $kwota_dzierzawy = isset($_POST['kwota_dzierzawy']) ? floatval(str_replace(',', '.', validateInput($_POST['kwota_dzierzawy']))) : 0;
    $suma_dzierzawa = $kwota_jed_netto * $ilosc_dzierzawa + $kwota_dzierzawy;
    $data_dzierzawa = $_POST['data_dzierzawa'];
    $kod_sklepu_dzierzawa = $_POST['kod_sklepu_dzierzawa'];
    $numer_faktury_dzierzawa = $_POST['numer_faktury_dzierzawa'];

    // Zabezpieczenie przed atakami SQL injection
    $kwota_jed_netto = mysqli_real_escape_string($conn, $kwota_jed_netto);
    $ilosc_dzierzawa = mysqli_real_escape_string($conn, $ilosc_dzierzawa);
    $stan_na_dzisiaj = mysqli_real_escape_string($conn, $stan_na_dzisiaj);
    $kwota_dzierzawy = mysqli_real_escape_string($conn, $kwota_dzierzawy);
    $suma_dzierzawa = mysqli_real_escape_string($conn, $suma_dzierzawa);
    $data_dzierzawa = mysqli_real_escape_string($conn, $data_dzierzawa);

    $query_numer_faktury_dzierzawa = "SELECT id FROM Faktura WHERE numer_faktury = '$numer_faktury_dzierzawa'";
    $result_numer_faktury_dzierzawa = mysqli_query($conn, $query_numer_faktury_dzierzawa);
    if(mysqli_num_rows($result_numer_faktury_dzierzawa) != 0){
        while($row = mysqli_fetch_assoc($result_numer_faktury_dzierzawa)){
            $faktura_dzierzawa = $row['id'];
        }
    }
    else{
        echo "Nie ma faktury o numerze: " . $numer_faktury_dzierzawa . "<br>";
    }

    if(!is_null($kod_sklepu_dzierzawa)){
        $query_kod_sklepu_dzierzawa = "SELECT IDdrukarki FROM DrukarkiInwentaryzacja di INNER JOIN Lokalizacja l 
                                    ON di.IDLokalizacji = l.IDLokalizacji WHERE l.Kod = '$kod_sklepu_dzierzawa'";
        $result_kod_sklepu_dzierzawa = mysqli_query($conn, $query_kod_sklepu_dzierzawa);
        if(mysqli_num_rows($result_kod_sklepu_dzierzawa) != 0){
            while($row = mysqli_fetch_assoc($result_kod_sklepu_dzierzawa)) {
                $iddrukarki_dzierzawa = $row['IDdrukarki'];
            }
            $query_dzierzawa = "INSERT INTO Dzierzawa (IDDrukarki, IDDostawcy, KwotaJedNetto, Ilosc, StanNaDzisiaj, KwotaDzierzawy, Suma, Data, IDFaktury) VALUES ('$iddrukarki_dzierzawa', '$iddostawcy_dzierzawa', '$kwota_jed_netto', '$ilosc_dzierzawa', '$stan_na_dzisiaj', '$kwota_dzierzawy', '$suma_dzierzawa', '$data_dzierzawa', '$faktura_dzierzawa')";
            $result_dzierzawa = mysqli_query($conn, $query_dzierzawa);

            if ($result_dzierzawa) {
                echo "Dane zostały dodane do tabeli Dzierzawa.". "<br>";
            } else {
                echo "Błąd podczas dodawania danych do tabeli Dzierzawa: " . mysqli_error($conn). "<br>";
            }
        }
        else{
            echo "Nie ma sklepu o podanym kodzie: " . $kod_sklepu_dzierzawa . "<br>";
        }
    }
    if($kod_sklepu_dzierzawa == 0){
        // Dodanie danych do tabeli Dzierzawa
        $query_dzierzawa = "INSERT INTO Dzierzawa (IDDrukarki, IDDostawcy, KwotaJedNetto, Ilosc, StanNaDzisiaj, KwotaDzierzawy, Suma, Data, IDFaktury) VALUES ('$iddrukarki_dzierzawa', '$iddostawcy_dzierzawa', '$kwota_jed_netto', '$ilosc_dzierzawa', '$stan_na_dzisiaj', '$kwota_dzierzawy', '$suma_dzierzawa', '$data_dzierzawa', '$faktura_dzierzawa')";
        $result_dzierzawa = mysqli_query($conn, $query_dzierzawa);

        if ($result_dzierzawa) {
            echo "Dane zostały dodane do tabeli Dzierzawa.<br>";
        } else {
            echo "Błąd podczas dodawania danych do tabeli Dzierzawa: " . mysqli_error($conn). "<br>";
        }
    }
}

// Obsługa dodawania danych do tabeli Naprawy
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_naprawa'])) {
    $iddostawcy_naprawa = mysqli_real_escape_string($conn, $_POST['iddostawcy_naprawa']);
    $kwota_naprawy = str_replace(',', '.', validateInput($_POST['kwota_naprawy']));
    $kwota_naprawy = mysqli_real_escape_string($conn, $kwota_naprawy);
    $data_naprawy = mysqli_real_escape_string($conn, $_POST['data_naprawy']);
    $kod_sklepu_naprawa = mysqli_real_escape_string($conn, $_POST['kod_sklepu_naprawa']);
    $numer_faktury_naprawa = mysqli_real_escape_string($conn, $_POST['numer_faktury_naprawa']);

    $query_kod_sklepu_naprawa = "SELECT IDLokalizacji FROM Lokalizacja l WHERE l.Kod = '$kod_sklepu_naprawa'";
    $result_kod_sklepu_naprawa = mysqli_query($conn, $query_kod_sklepu_naprawa);

    $query_numer_faktury_naprawa = "SELECT id FROM Faktura WHERE numer_faktury = '$numer_faktury_naprawa'";
    $result_numer_faktury_naprawa = mysqli_query($conn, $query_numer_faktury_naprawa);

    if (mysqli_num_rows($result_kod_sklepu_naprawa) != 0 && mysqli_num_rows($result_numer_faktury_naprawa) != 0) {
        $row = mysqli_fetch_assoc($result_kod_sklepu_naprawa);
        $idlokalizacji_naprawa = $row['IDLokalizacji'];

        $row = mysqli_fetch_assoc($result_numer_faktury_naprawa);
        $faktura_naprawy = $row['id'];

        $query_naprawa = "INSERT INTO Naprawy (IDDostawcy, IDLokalizacji, Kwota, DataNaprawy, IDFaktury) VALUES ('$iddostawcy_naprawa', '$idlokalizacji_naprawa', '$kwota_naprawy', '$data_naprawy', '$faktura_naprawy')";
        $result_naprawa = mysqli_query($conn, $query_naprawa);

        if ($result_naprawa) {
            echo "Dane zostały dodane do tabeli Naprawy.";
        } else {
            echo "Błąd podczas dodawania danych do tabeli Naprawy: " . mysqli_error($conn);
        }
    }
    elseif(mysqli_num_rows($result_kod_sklepu_naprawa) == 0) {
        echo "Nie znaleziono lokalizacji.";
    }
    elseif(mysqli_num_rows($result_numer_faktury_naprawa) == 0){
        echo "Nie znaleziono faktury";
    }
}

// Obsługa dodawania danych do tabeli Tonery
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_toner'])) {
    $idlokalizacji_toner = $_POST['idlokalizacji_toner'];
    $kwota_toner = str_replace(',', '.', validateInput($_POST['kwota_toner']));
    $ilosc_toner = $_POST['ilosc_toner'];
    $suma_toner = $kwota_toner*$ilosc_toner;
    $data_toner = $_POST['data_toner'];
    $kod_sklepu_toner = $_POST['kod_sklepu_toner'];
    $numer_faktury_toner = $_POST['numer_faktury_toner'];

    // Zabezpieczenie przed atakami SQL injection
    $kwota_toner = mysqli_real_escape_string($conn, $kwota_toner);
    $ilosc_toner = mysqli_real_escape_string($conn, $ilosc_toner);
    $suma_toner = mysqli_real_escape_string($conn, $suma_toner);
    $data_toner = mysqli_real_escape_string($conn, $data_toner);
    $faktura_toner = mysqli_real_escape_string($conn, $faktura_toner);


    $query_kod_sklepu_toner = "SELECT IDLokalizacji FROM Lokalizacja l WHERE l.Kod = '$kod_sklepu_toner'";
    $result_kod_sklepu_toner = mysqli_query($conn, $query_kod_sklepu_toner);

    $query_numer_faktury_toner = "SELECT id FROM Faktura WHERE numer_faktury = '$numer_faktury_toner'";
    $result_numer_faktury_toner = mysqli_query($conn, $query_numer_faktury_toner);


    if(mysqli_num_rows($result_kod_sklepu_toner) != 0 && mysqli_num_rows($result_numer_faktury_toner) != 0){
        $row = mysqli_fetch_assoc($result_kod_sklepu_toner);
        $idlokalizacji_toner = $row['IDLokalizacji'];

        $row = mysqli_fetch_assoc($result_numer_faktury_toner);
        $faktura_toner = $row['id'];

        // Dodanie danych do tabeli Tonery
        $query_toner = "INSERT INTO Tonery (IDLokalizacji, Kwota, Ilosc, Suma, Data, IDFaktury) VALUES ('$idlokalizacji_toner', '$kwota_toner', '$ilosc_toner', '$suma_toner', '$data_toner', '$faktura_toner')";
        $result_toner = mysqli_query($conn, $query_toner);

        if ($result_toner) {
            echo "Dane zostały dodane do tabeli Tonery.";
        } else {
            echo "Błąd podczas dodawania danych do tabeli Tonery: " . mysqli_error($conn);
        }
    }
    elseif(mysqli_num_rows($result_kod_sklepu_toner) == 0) {
        echo "Nie znaleziono lokalizacji.";
    }
    elseif(mysqli_num_rows($result_numer_faktury_toner) == 0){
        echo "Nie znaleziono faktury";
    }
}

// Obsługa dodawania danych do tabeli Faktura
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

// Pobranie dostępnych drukarek dla comboboxa w dzierżawie
$query_drukarki_dzierzawa = "SELECT di.IDdrukarki, CONCAT(dm.Producent, ' ', dm.Model, ' - ', di.NumerSeryjny, ' - ', l.Kod, '-', l.Nazwa_Lokalizacji ,' - ', di.AdresIP, ' - ', di.Lokalizacja) AS Model FROM DrukarkiInwentaryzacja di LEFT JOIN DrukarkiModele dm ON di.IDModeluDrukarki = dm.IDDrukarki LEFT JOIN Lokalizacja l ON di.IDLokalizacji = l.IDLokalizacji WHERE di.IDDostawcy > 1";
$result_drukarki_dzierzawa = mysqli_query($conn, $query_drukarki_dzierzawa);

// Pobranie dostawców dla comboboxa w dzierżawie
$query_dostawcy_dzierzawa = "SELECT IDDostawcy, Nazwa FROM Dostawca";
$result_dostawcy_dzierzawa = mysqli_query($conn, $query_dostawcy_dzierzawa);

// Pobranie dostawców dla comboboxa w naprawie
$query_dostawcy_naprawa = "SELECT IDDostawcy, Nazwa FROM Dostawca";
$result_dostawcy_naprawa = mysqli_query($conn, $query_dostawcy_naprawa);

// Pobranie faktur dla comboboxa w dzierzawie
$query_faktura_dzierzawa = "SELECT id, numer_faktury FROM Faktura";
$result_faktura_dzierzawa = mysqli_query($conn, $query_faktura_dzierzawa);

// Pobranie faktur dla comboboxa w naprawach
$query_faktura_naprawa = "SELECT id, numer_faktury FROM Faktura";
$result_faktura_naprawa = mysqli_query($conn, $query_faktura_naprawa);

// Pobranie faktur dla comboboxa w tonerach
$query_faktura_tonery = "SELECT id, numer_faktury FROM Faktura";
$result_faktura_tonery = mysqli_query($conn, $query_faktura_tonery);

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
            <h3>Dzierżawa</h3>
            <div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="iddrukarki_dzierzawa">Wybierz drukarkę:</label><br>
                    <select id="iddrukarki_dzierzawa" name="iddrukarki_dzierzawa">
                        <?php
                        while ($row_drukarki_dzierzawa = mysqli_fetch_assoc($result_drukarki_dzierzawa)) {
                            echo "<option value='" . $row_drukarki_dzierzawa['IDdrukarki'] . "'>" . $row_drukarki_dzierzawa['Model'] . "</option>";
                        }
                        ?>
                    </select><br>
                    <label for="iddostawcy_dzierzawa">Wybierz dostawcę:</label><br>
                    <select id="iddostawcy_dzierzawa" name="iddostawcy_dzierzawa">
                        <?php
                        while ($row_dostawcy_dzierzawa = mysqli_fetch_assoc($result_dostawcy_dzierzawa)) {
                            echo "<option value='" . $row_dostawcy_dzierzawa['IDDostawcy'] . "'>" . $row_dostawcy_dzierzawa['Nazwa'] . "</option>";
                        }
                        ?>
                    </select><br>
                    <label for="numer_faktury_dzierzawa">Wpisz fakturę:</label><br>
                    <input type="text" id="numer_faktury_dzierzawa" name="numer_faktury_dzierzawa" required><br>
                    <label for="kod_sklepu_dzierzawa">Kod sklepu:</label><br>
                    <input type="text" id="kod_sklepu_dzierzawa" name="kod_sklepu_dzierzawa"><br>
                    <label for="kwota_jed_netto">Kwota jednostkowa netto:</label><br>
                    <input type="text" id="kwota_jed_netto" name="kwota_jed_netto"><br>
                    <label for="ilosc_dzierzawa">Ilość:</label><br>
                    <input type="text" id="ilosc_dzierzawa" name="ilosc_dzierzawa"><br>
                    <label for="stan_na_dzisiaj">Stan na dzisiaj:</label><br>
                    <input type="text" id="stan_na_dzisiaj" name="stan_na_dzisiaj"><br>
                    <label for="kwota_dzierzawy">Kwota dzierżawy:</label><br>
                    <input type="text" id="kwota_dzierzawy" name="kwota_dzierzawy"><br>
                    <label for="data_dzierzawa">Data:</label><br>
                    <input type="date" id="data_dzierzawa" name="data_dzierzawa" required><br>
                    <input type="submit" name="submit_dzierzawa" value="Dodaj">
                </form>
            </div>


        </div>

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
            <h3>Naprawa</h3>
            <div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="iddostawcy_naprawa">Wybierz dostawcę:</label><br>
                    <select id="iddostawcy_naprawa" name="iddostawcy_naprawa">
                        <?php
                        while ($row_dostawcy_naprawa = mysqli_fetch_assoc($result_dostawcy_naprawa)) {
                            echo "<option value='" . $row_dostawcy_naprawa['IDDostawcy'] . "'>" . $row_dostawcy_naprawa['Nazwa'] . "</option>";
                        }
                        ?>
                    </select><br>
                    <label for="kod_sklepu_naprawa">Wpisz kod lokalizacji:</label><br>
                    <input type="text" id="kod_sklepu_naprawa" name="kod_sklepu_naprawa" required><br>
                    <label for="faktura_naprawy">Wpisz fakturę:</label><br>
                    <input type="text" id="numer_faktury_naprawa" name="numer_faktury_naprawa" required><br>
                    <label for="kwota_naprawy">Kwota naprawy:</label><br>
                    <input type="text" id="kwota_naprawy" name="kwota_naprawy" required><br>
                    <label for="data_naprawy">Data naprawy:</label><br>
                    <input type="date" id="data_naprawy" name="data_naprawy" required><br>
                    <input type="submit" name="submit_naprawa" value="Dodaj">
                </form>
            </div>

        </div>

        <div class="sekcja">
            <h3>Dodaj do tabeli Tonery</h3>
            <div>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="kod_sklepu_toner">Wpisz kod lokalizacji:</label><br>
                    <input type="text" id="kod_sklepu_toner" name="kod_sklepu_toner" required><br>
                    <label for="faktura_toner">Wpisz fakturę:</label><br>
                    <input type="text" id="numer_faktury_toner" name="numer_faktury_toner" required><br>
                    <label for="kwota_toner">Kwota:</label><br>
                    <input type="text" id="kwota_toner" name="kwota_toner" required><br>
                    <label for="ilosc_toner">Ilość:</label><br>
                    <input type="text" id="ilosc_toner" name="ilosc_toner" required><br>
                    <label for="data_toner">Data:</label><br>
                    <input type="date" id="data_toner" name="data_toner" required><br>
                    <input type="submit" name="submit_toner" value="Dodaj">
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
