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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_dzierzawa'])) {
    $iddrukarki_dzierzawa = $_POST['iddrukarki_dzierzawa'];
    $iddostawcy_dzierzawa = $_POST['iddostawcy_dzierzawa'];
    $kwota_jed_netto = isset($_POST['kwota_jed_netto']) ? floatval(str_replace(',', '.', validateInput($_POST['kwota_jed_netto']))) : 0;
    $ilosc_dzierzawa = isset($_POST['ilosc_dzierzawa']) ? intval($_POST['ilosc_dzierzawa']) : 0;
    $stan_na_dzisiaj = isset($_POST['stan_na_dzisiaj']) ? intval($_POST['stan_na_dzisiaj']) : 0;
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_dzierzawa'])) {
    $iddzierzawy = $_POST['iddzierzawy'];

    // Zabezpieczenie przed atakami SQL injection
    $iddzierzawy = mysqli_real_escape_string($conn, $iddzierzawy);

    // Usuń użytkownika z bazy danych
    $delete_query = "DELETE FROM Dzierzawa WHERE IDDzierzawy='$iddzierzawy'";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        echo "Rekord został prawidłowo usunięty<br>";
    } else {
        echo "Błąd podczas usuwania rekordu z tabeli Dzierzawa: " . mysqli_error($conn). "<br>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_dzierzawa'])) {
    $iddzierzawy = $_POST['iddzierzawy'];
    $new_iddrukarki_dzierzawa = $_POST['new_iddrukarki_dzierzawa'];
    $new_iddostawcy_dzierzawa = $_POST['new_iddostawcy_dzierzawa'];
    $new_kwota_jed_netto = isset($_POST['new_kwota_jed_netto']) ? floatval(str_replace(',', '.', validateInput($_POST['new_kwota_jed_netto']))) : 0;
    $new_ilosc_dzierzawa = isset($_POST['new_ilosc_dzierzawa']) ? intval($_POST['new_ilosc_dzierzawa']) : 0;
    $new_stan_na_dzisiaj = isset($_POST['new_stan_na_dzisiaj']) ? intval($_POST['new_stan_na_dzisiaj']) : 0;
    $new_kwota_dzierzawy = isset($_POST['new_kwota_dzierzawy']) ? floatval(str_replace(',', '.', validateInput($_POST['new_kwota_dzierzawy']))) : 0;
    $new_suma_dzierzawa = $kwota_jed_netto * $ilosc_dzierzawa + $kwota_dzierzawy;
    $new_data_dzierzawa = $_POST['new_data_dzierzawa']. "-01";
    $new_kod_sklepu_dzierzawa = $_POST['new_kod_sklepu_dzierzawa'];
    $new_numer_faktury_dzierzawa = $_POST['new_numer_faktury_dzierzawa'];

    $new_query_numer_faktury_dzierzawa = "SELECT id FROM Faktura WHERE numer_faktury = '$new_numer_faktury_dzierzawa'";
    $new_result_numer_faktury_dzierzawa = mysqli_query($conn, $new_query_numer_faktury_dzierzawa);
    if(mysqli_num_rows($new_result_numer_faktury_dzierzawa) != 0){
        while($row = mysqli_fetch_assoc($new_result_numer_faktury_dzierzawa)){
            $new_faktura_dzierzawa = $row['id'];
        }
    }
    else{
        echo "Nie ma faktury o numerze: " . $new_numer_faktury_dzierzawa . "<br>";
    }

    if(!is_null($new_kod_sklepu_dzierzawa)){
        $new_query_kod_sklepu_dzierzawa = "SELECT IDdrukarki FROM DrukarkiInwentaryzacja di INNER JOIN Lokalizacja l 
                                    ON di.IDLokalizacji = l.IDLokalizacji WHERE l.Kod = '$new_kod_sklepu_dzierzawa'";
        $new_result_kod_sklepu_dzierzawa = mysqli_query($conn, $new_query_kod_sklepu_dzierzawa);
        if(mysqli_num_rows($new_result_kod_sklepu_dzierzawa) != 0){
            while($row = mysqli_fetch_assoc($new_result_kod_sklepu_dzierzawa)) {
                $new_iddrukarki_dzierzawa = $row['IDdrukarki'];
            }
            $update_query = "UPDATE Dzierzawa SET
                                IDDrukarki = '$new_iddrukarki_dzierzawa',
                                IDDostawcy = '$new_iddostawcy_dzierzawa',
                                KwotaJedNetto = '$new_kwota_jed_netto',
                                Ilosc = '$new_ilosc_dzierzawa',
                                StanNaDzisiaj = '$new_stan_na_dzisiaj',
                                KwotaDzierzawy = '$new_kwota_dzierzawy',
                                Suma = '$new_suma_dzierzawa',
                                IDFaktury = '$new_faktura_dzierzawa',
                                Data = '$new_data_dzierzawa'
                                WHERE IDDzierzawy = $iddzierzawy";

            $update_result = mysqli_query($conn, $update_query);
            if ($update_result) {
                echo "Dane zostały zaktualizowane w tabeli Dzierzawa.". "<br>";
            } else {
                echo "Błąd podczas dodawania danych do tabeli Dzierzawa: " . mysqli_error($conn). "<br>";
            }
        }
        else{
            echo "Nie ma sklepu o podanym kodzie: " . $kod_sklepu_dzierzawa . "<br>";
        }
    }
    if($new_kod_sklepu_dzierzawa == 0){
        // Dodanie danych do tabeli Dzierzawa
        $update_query = "UPDATE Dzierzawa SET
                                IDDrukarki = '$new_iddrukarki_dzierzawa',
                                IDDostawcy = '$new_iddrukarki_dzierzawa',
                                KwotaJedNetto = '$new_kwota_jed_netto',
                                Ilosc = '$new_ilosc_dzierzawa',
                                StanNaDzisiaj = '$new_stan_na_dzisiaj',
                                KwotaDzierzawy = '$new_kwota_dzierzawy',
                                Suma = '$new_suma_dzierzawa',
                                IDFaktury = '$new_faktura_dzierzawa'";
            $update_result = mysqli_query($conn, $update_query);
        echo $update_query;
        if ($result_dzierzawa) {
            echo "Dane zostały dodane do tabeli Dzierzawa.<br>";
        } else {
            echo "Błąd podczas dodawania danych do tabeli Dzierzawa: " . mysqli_error($conn). "<br>";
        }
    }
}


// Pobranie dostępnych drukarek dla comboboxa w dzierżawie
$query_drukarki_dzierzawa_new = "SELECT di.IDdrukarki, CONCAT(dm.Producent, ' ', dm.Model, ' - ', di.NumerSeryjny, ' - ', l.Kod, '-', l.Nazwa_Lokalizacji ,' - ', di.AdresIP, ' - ', di.Lokalizacja) AS Model FROM DrukarkiInwentaryzacja di LEFT JOIN DrukarkiModele dm ON di.IDModeluDrukarki = dm.IDDrukarki LEFT JOIN Lokalizacja l ON di.IDLokalizacji = l.IDLokalizacji WHERE di.IDDostawcy > 1";
$result_drukarki_dzierzawa_new = mysqli_query($conn, $query_drukarki_dzierzawa_new);

// Pobranie dostawców dla comboboxa w dzierżawie
$query_dostawcy_dzierzawa_new = "SELECT IDDostawcy, Nazwa FROM Dostawca";
$result_dostawcy_dzierzawa_new = mysqli_query($conn, $query_dostawcy_dzierzawa_new);
// Pobranie dostępnych drukarek dla comboboxa w dzierżawie
$query_drukarki_dzierzawa_edit = "SELECT di.IDdrukarki, CONCAT(dm.Producent, ' ', dm.Model, ' - ', di.NumerSeryjny, ' - ', l.Kod, '-', l.Nazwa_Lokalizacji ,' - ', di.AdresIP, ' - ', di.Lokalizacja) AS Model FROM DrukarkiInwentaryzacja di LEFT JOIN DrukarkiModele dm ON di.IDModeluDrukarki = dm.IDDrukarki LEFT JOIN Lokalizacja l ON di.IDLokalizacji = l.IDLokalizacji WHERE di.IDDostawcy > 1";
$result_drukarki_dzierzawa_edit = mysqli_query($conn, $query_drukarki_dzierzawa_edit);

// Pobranie dostawców dla comboboxa w dzierżawie
$query_dostawcy_dzierzawa_edit = "SELECT IDDostawcy, Nazwa FROM Dostawca";
$result_dostawcy_dzierzawa_edit = mysqli_query($conn, $query_dostawcy_dzierzawa_edit);

$query_dzierzawa_select = "SELECT
                        dz.IDDzierzawy, dz.KwotaJedNetto, dz.Ilosc, dz.StanNaDzisiaj, dz.KwotaDzierzawy, dz.Suma,
                        DATE_FORMAT(dz.Data,'%Y-%m') AS DataDzierzawy,
                        CONCAT(dm.Producent, ' ', dm.Model) AS ModelDrukarki, do.Nazwa AS NazwaDostawcy,
                        f.numer_faktury,
                        CONCAT(l.Kod, ' - ', l.Nazwa_Lokalizacji) AS Lokalizacja,
                        l.Kod AS KodSklepu
                    FROM
                        Dzierzawa dz
                    JOIN
                        DrukarkiInwentaryzacja di ON dz.IDDrukarki = di.IDDrukarki
                    JOIN
                        DrukarkiModele dm ON di.IDModeluDrukarki = dm.IDDrukarki
                    INNER JOIN
                        Lokalizacja l ON di.IDLokalizacji = l.IDLokalizacji
                    INNER JOIN
                        Dostawca do ON dz.IDDostawcy = do.IDDostawcy
                    INNER JOIN
                        Faktura f ON dz.IDFaktury = f.id
                    ORDER BY DataDzierzawy ASC, l.IDLokalizacji ASC, di.IDDostawcy ASC";
$result_dzierzawa_select = mysqli_query($conn, $query_dzierzawa_select);

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
    <header><h2>Dzierżawy</h2></header>
    <div id="body">
        <div class="sekcja">
            <h3>Dodaj dzierżawę</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="iddrukarki_dzierzawa">Imię:</label>
                    <select id="iddrukarki_dzierzawa" name="iddrukarki_dzierzawa">
                        <?php
                        while ($row_drukarki_dzierzawa = mysqli_fetch_assoc($result_drukarki_dzierzawa_new)) {
                            echo "<option value='" . $row_drukarki_dzierzawa['IDdrukarki'] . "'>" . $row_drukarki_dzierzawa['Model'] . "</option>";
                        }
                        ?>
                    </select><br>
                </div>
               <div class="form-group">
                    <label for="iddostawcy_dzierzawa">Wybierz dostawcę:</label><br>
                    <select id="iddostawcy_dzierzawa" name="iddostawcy_dzierzawa">
                        <?php
                        while ($row_dostawcy_dzierzawa = mysqli_fetch_assoc($result_dostawcy_dzierzawa_new)) {
                            echo "<option value='" . $row_dostawcy_dzierzawa['IDDostawcy'] . "'>" . $row_dostawcy_dzierzawa['Nazwa'] . "</option>";
                        }
                        ?>
                    </select><br>
                </div>
                <div class="form_group">
                    <label for="numer_faktury_dzierzawa">Wpisz fakturę:</label><br>
                    <input type="text" id="numer_faktury_dzierzawa" name="numer_faktury_dzierzawa" required><br>
                </div>
                <div class="form-group">
                    <label for="kod_sklepu_dzierzawa">Kod sklepu:</label><br>
                    <input type="text" id="kod_sklepu_dzierzawa" name="kod_sklepu_dzierzawa"><br>
                </div>
                <div class="form-group">
                    <label for="kwota_jed_netto">Kwota jednostkowa netto:</label><br>
                    <input type="text" id="kwota_jed_netto" name="kwota_jed_netto"><br>
                </div>
                <div class="form-group">
                    <label for="ilosc_dzierzawa">Ilość:</label><br>
                    <input type="text" id="ilosc_dzierzawa" name="ilosc_dzierzawa"><br>
                </div>
                <div class="form-group">
                    <label for="stan_na_dzisiaj">Stan na dzisiaj:</label><br>
                    <input type="text" id="stan_na_dzisiaj" name="stan_na_dzisiaj"><br>
                </div>
                <div class="form-group">
                    <label for="kwota_dzierzawy">Kwota dzierżawy:</label><br>
                    <input type="text" id="kwota_dzierzawy" name="kwota_dzierzawy"><br>
                </div>
                <div class="form-group">
                    <label for="data_dzierzawa">Data:</label><br>
                    <input type="date" id="data_dzierzawa" name="data_dzierzawa" required><br>
                </div>
                <input type="submit" name="submit_dzierzawa" value="Dodaj dzierżawę">
            </form>
        </div>


        <div class="sekcja">
            <h3>Dzierżawa</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Dostawca</th>
                    <th>Nowy Dostawca</th>
                    <th>Kod sklepu</th>
                    <th>Nowy Kod sklepu</th>
                    <th>Kwota jednostkowa Netto</th>
                    <th>Nowa Kwota jednostkowa Netto</th>
                    <th>Ilość</th>
                    <th>Nowa Ilość</th>
                    <th>Stan na dzisiaj</th>
                    <th>Nowy Stan na dzisiaj</th>
                    <th>Kwota dzierżawy</th>
                    <th>Nowa Kwota dzierżawy</th>
                    <th>Data dzierżawy</th>
                    <th>Nowa Data dzierżawy</th>
                    <th>Faktura</th>
                    <th>Nowa Faktura</th>
                    <th>Edycja</th>
                    <th>Usuwanie</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result_dzierzawa_select)): ?>
                    <tr>
                        <td><?php echo $row['IDDzierzawy']; ?></td>
                        <td><?php echo $row['NazwaDostawcy']; ?></td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <td>
                            <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                            <select id="new_iddostawcy_dzierzawa" name="new_iddostawcy_dzierzawa">
                                <?php
                                mysqli_data_seek($result_dostawcy_dzierzawa_edit, 0);
                                while ($row_dostawcy_dzierzawa = mysqli_fetch_assoc($result_dostawcy_dzierzawa_edit)) {
                                    echo "<option value='" . $row_dostawcy_dzierzawa['IDDostawcy'] . "'>" . $row_dostawcy_dzierzawa['Nazwa'] . "</option>";
                                }
                                ?>
                            </select><br>
                        </td>
                        <td><?php echo $row['Lokalizacja']; ?></td>
                        <td>
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="text" name="new_kod_sklepu_dzierzawa" value="<?php echo $row['KodSklepu']; ?>" >
                                <select id="new_iddrukarki_dzierzawa" name="new_iddrukarki_dzierzawa">
                                    <?php
                                    mysqli_data_seek($result_drukarki_dzierzawa_edit, 0);
                                    while ($row_drukarki_dzierzawa = mysqli_fetch_assoc($result_drukarki_dzierzawa_edit)) {
                                        echo "<option value='" . $row_drukarki_dzierzawa['IDdrukarki'] . "'>" . $row_drukarki_dzierzawa['Model'] . "</option>";
                                    }
                                    ?>
                                </select><br>
                        </td>
                        <td><?php echo $row['KwotaJedNetto']; ?></td>
                        <td>
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="text" name="new_kwota_jed_netto" value="<?php echo $row['KwotaJedNetto']; ?>" required>
                        </td>
                            <td><?php echo $row['Ilosc']; ?></td>
                        <td>
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="text" name="new_ilosc_dzierzawa" value="<?php echo $row['Ilosc']; ?>" required>
                        </td>
                            <td><?php echo $row['StanNaDzisiaj']; ?></td>
                            <td>
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="text" name="new_stan_na_dzisiaj" value="<?php echo $row['StanNaDzisiaj']; ?>" required>
                        </td>
                            <td><?php echo $row['KwotaDzierzawy']; ?></td>
                        <td>
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="text" name="new_kwota_dzierzawy" value="<?php echo $row['KwotaDzierzawy']; ?>" required>
                        </td>
                            <td><?php echo $row['DataDzierzawy']; ?></td>
                            <td>
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="text" name="new_data_dzierzawa" value="<?php echo $row['DataDzierzawy']; ?>" required>
                        </td>
                            <td><?php echo $row['numer_faktury']; ?></td>
                            <td>
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="text" name="new_numer_faktury_dzierzawa" value="<?php echo $row['numer_faktury']; ?>" required>
                        </td>
                        <td>
                            <!-- Przycisk do zatwierdzenia zmian -->
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="submit" name="edit_dzierzawa" value="Zapisz zmiany">
                        </td>
                        <td>
                            <!-- Formularz dla usuwania użytkownika -->
                                <input type="hidden" name="iddzierzawy" value="<?php echo $row['IDDzierzawy']; ?>">
                                <input type="submit" name="delete_dzierzawa" value="Usuń">
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
