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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_tonery'])) {
    $idtoneru = $_POST['idtoneru'];

    // Zabezpieczenie przed atakami SQL injection
    $idnaprawy = mysqli_real_escape_string($conn, $idnaprawy);

    // Usuń użytkownika z bazy danych
    $delete_query = "DELETE FROM Tonery WHERE IDToneru='$idtoneru'";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        echo "Rekord został prawidłowo usunięty<br>";
    } else {
        echo "Błąd podczas usuwania rekordu z tabeli Dzierzawa: " . mysqli_error($conn). "<br>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_toner'])) {
    $idtoneru = $_POST['idtoneru'];
    $new_idlokalizacji_toner = $_POST['new_idlokalizacji_toner'];
    $new_kwota_toner = str_replace(',', '.', validateInput($_POST['new_kwota_toner']));
    $new_ilosc_toner = $_POST['new_ilosc_toner'];
    $new_suma_toner = $new_kwota_toner*$new_ilosc_toner;
    $new_data_toner = $_POST['new_data_toner'] . "-01";
    $new_kod_sklepu_toner = $_POST['new_kod_sklepu_toner'];
    $new_numer_faktury_toner = $_POST['new_numer_faktury_toner'];

    // Zabezpieczenie przed atakami SQL injection
    $new_faktura_toner = mysqli_real_escape_string($conn, $faktura_toner);


    $query_kod_sklepu_toner = "SELECT IDLokalizacji FROM Lokalizacja l WHERE l.Kod = '$new_kod_sklepu_toner'";
    $result_kod_sklepu_toner = mysqli_query($conn, $query_kod_sklepu_toner);

    $query_numer_faktury_toner = "SELECT id FROM Faktura WHERE numer_faktury = '$new_numer_faktury_toner'";
    $result_numer_faktury_toner = mysqli_query($conn, $query_numer_faktury_toner);


    if(mysqli_num_rows($result_kod_sklepu_toner) != 0 && mysqli_num_rows($result_numer_faktury_toner) != 0){
        $row = mysqli_fetch_assoc($result_kod_sklepu_toner);
        $new_idlokalizacji_toner = $row['IDLokalizacji'];

        $row = mysqli_fetch_assoc($result_numer_faktury_toner);
        $new_faktura_toner = $row['id'];

        // Dodanie danych do tabeli Tonery
        $query_toner = "UPDATE Tonery SET
                        IDLokalizacji = '$new_idlokalizacji_toner',
                        IDFaktury = '$new_faktura_toner',
                        Kwota = '$new_kwota_toner',
                        Ilosc = '$new_ilosc_toner',
                        Suma  = '$new_suma_toner',
                        Data = '$new_data_toner'
                        WHERE IDToneru = '$idtoneru'
        ";
        $result_toner = mysqli_query($conn, $query_toner);

        if ($result_toner) {
            echo "Dane zostały zaktualizowane w tabeli Tonery.";
        } else {
            echo "Błąd podczas aktualizacji danych do tabeli Tonery: " . mysqli_error($conn);
        }
    }
    elseif(mysqli_num_rows($result_kod_sklepu_toner) == 0) {
        echo "Nie znaleziono lokalizacji.";
    }
    elseif(mysqli_num_rows($result_numer_faktury_toner) == 0){
        echo "Nie znaleziono faktury";
    }
}

$query_tonery_select = "SELECT 
                        tn.Kwota, tn.Ilosc, tn.Suma, f.numer_faktury, 
                        DATE_FORMAT(tn.Data,'%Y-%m') AS DataTonerow, 
                        CONCAT(l.Kod, ' - ', l.Nazwa_Lokalizacji) AS Lokalizacja,
                        l.IDLokalizacji, tn.IDToneru, l.Kod AS KodSklepu
                    FROM 
                        Tonery tn
                    JOIN
                        Lokalizacja l ON tn.IDLokalizacji = l.IDLokalizacji
                    JOIN
                        Faktura f ON tn.IDFaktury = f.id
                    ORDER BY tn.Data ASC, tn.IDLokalizacji ASC";
$result_tonery_select = mysqli_query($conn, $query_tonery_select);

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
        <div class="sekcja">
            <h3>Dzierżawa</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Kod sklepu</th>
                    <th>Nowy Kod sklepu</th>
                    <th>Kwota toneru</th>
                    <th>Nowa Kwota toneru</th>
                    <th>Ilość</th>
                    <th>Nowa Ilość</th>
                    <th>Data toneru</th>
                    <th>Nowa Data toneru</th>
                    <th>Faktura</th>
                    <th>Nowa Faktura</th>
                    <th>Edycja</th>
                    <th>Usuwanie</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result_tonery_select)): ?>
                    <tr>
                        <td><?php echo $row['IDToneru']; ?></td>
                        <td><?php echo $row['Lokalizacja']; ?></td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <td>
                                <input type="hidden" name="idtoneru" value="<?php echo $row['IDToneru']; ?>">
                                <input type="text" name="new_kod_sklepu_toner" value="<?php echo $row['KodSklepu']; ?>" >
                        </td>
                        <td><?php echo $row['Kwota']; ?></td>
                        <td>
                                <input type="hidden" name="idtoneru" value="<?php echo $row['IDToneru']; ?>">
                                <input type="text" name="new_kwota_toner" value="<?php echo $row['Kwota']; ?>" required>
                        </td>
                            <td><?php echo $row['Ilosc']; ?></td>
                            <td>
                                <input type="hidden" name="idtoneru" value="<?php echo $row['IDToneru']; ?>">
                                <input type="text" name="new_ilosc_toner" value="<?php echo $row['Ilosc']; ?>" required>
                            </td>
                            <td><?php echo $row['DataTonerow']; ?></td>
                            <td>
                                <input type="hidden" name="idtoneru" value="<?php echo $row['IDToneru']; ?>">
                                <input type="text" name="new_data_toner" value="<?php echo $row['DataTonerow']; ?>" required>
                        </td>
                            <td><?php echo $row['numer_faktury']; ?></td>
                            <td>
                                <input type="hidden" name="idtoneru" value="<?php echo $row['IDToneru']; ?>">
                                <input type="text" name="new_numer_faktury_toner" value="<?php echo $row['numer_faktury']; ?>" required>
                        </td>
                        <td>
                            <!-- Przycisk do zatwierdzenia zmian -->
                                <input type="hidden" name="idtoneru" value="<?php echo $row['IDToneru']; ?>">
                                <input type="submit" name="edit_toner" value="Zapisz zmiany">
                        </td>
                        <td>
                            <!-- Formularz dla usuwania użytkownika -->
                                <input type="hidden" name="idtoneru" value="<?php echo $row['IDToneru']; ?>">
                                <input type="submit" name="delete_tonery" value="Usuń">
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
