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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_naprawa'])) {
    $idnaprawy = $_POST['idnaprawy'];

    // Zabezpieczenie przed atakami SQL injection
    $idnaprawy = mysqli_real_escape_string($conn, $idnaprawy);

    // Usuń użytkownika z bazy danych
    $delete_query = "DELETE FROM Naprawy WHERE IDNaprawy='$idnaprawy'";
    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        echo "Rekord został prawidłowo usunięty<br>";
    } else {
        echo "Błąd podczas usuwania rekordu z tabeli Dzierzawa: " . mysqli_error($conn). "<br>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_naprawa'])) {
    $idnaprawy = $_POST['idnaprawy'];
    $new_iddostawcy_naprawa = mysqli_real_escape_string($conn, $_POST['new_iddostawcy_naprawa']);
    $new_kwota_naprawy = str_replace(',', '.', validateInput($_POST['new_kwota_naprawy']));
    $new_kwota_naprawy = mysqli_real_escape_string($conn, $new_kwota_naprawy);
    $new_data_naprawy = mysqli_real_escape_string($conn, $_POST['new_data_naprawy']);
    $new_kod_sklepu_naprawa = mysqli_real_escape_string($conn, $_POST['new_kod_sklepu_naprawa']);
    $new_numer_faktury_naprawa = mysqli_real_escape_string($conn, $_POST['new_numer_faktury_naprawa']);
    $new_data_naprawy = $new_data_naprawy . '-01';
    $query_kod_sklepu_naprawa = "SELECT IDLokalizacji FROM Lokalizacja l WHERE l.Kod = '$new_kod_sklepu_naprawa'";
    $result_kod_sklepu_naprawa = mysqli_query($conn, $query_kod_sklepu_naprawa);

    $query_numer_faktury_naprawa = "SELECT id FROM Faktura WHERE numer_faktury = '$new_numer_faktury_naprawa'";
    $result_numer_faktury_naprawa = mysqli_query($conn, $query_numer_faktury_naprawa);

    if (mysqli_num_rows($result_kod_sklepu_naprawa) != 0 && mysqli_num_rows($result_numer_faktury_naprawa) != 0) {
        $row = mysqli_fetch_assoc($result_kod_sklepu_naprawa);
        $new_idlokalizacji_naprawa = $row['IDLokalizacji'];

        $row = mysqli_fetch_assoc($result_numer_faktury_naprawa);
        $new_idfaktura_naprawy = $row['id'];

        $query_naprawa = "UPDATE Naprawy SET
                            IDDostawcy = '$new_iddostawcy_naprawa',
                            IDLokalizacji = '$new_idlokalizacji_naprawa',
                            IDFaktury = '$new_idfaktura_naprawy',
                            Kwota = '$new_kwota_naprawy',
                            DataNaprawy = '$new_data_naprawy'
                            WHERE IDNaprawy = '$idnaprawy'";
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

$query_naprawa_select = "SELECT 
                        np.IDNaprawy, do.Nazwa, 
                        CONCAT(l.Kod, ' - ', l.Nazwa_Lokalizacji) AS Lokalizacja, 
                        do.Nazwa AS NazwaDostawcy,
                        l.Kod AS KodSklepu,
                        np.Kwota, f.numer_faktury, 
                        DATE_FORMAT(np.DataNaprawy,'%Y-%m') AS DataNaprawy
                    FROM 
                        Naprawy np
                    JOIN 
                        Dostawca do ON np.IDDostawcy = do.IDDostawcy
                    JOIN
                        Lokalizacja l ON np.IDLokalizacji = l.IDLokalizacji
                    JOIN
                        Faktura f ON np.IDFaktury = f.id
                    ORDER BY DataNaprawy ASC, np.IDLokalizacji ASC";
$result_naprawa_select = mysqli_query($conn, $query_naprawa_select);

// Pobranie faktur dla comboboxa w naprawach
$query_faktura_naprawa = "SELECT id, numer_faktury FROM Faktura";
$result_faktura_naprawa = mysqli_query($conn, $query_faktura_naprawa);
// Pobranie dostawców dla comboboxa w naprawie
$query_dostawcy_naprawa = "SELECT IDDostawcy, Nazwa FROM Dostawca";
$result_dostawcy_naprawa = mysqli_query($conn, $query_dostawcy_naprawa);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <link rel="stylesheet" href="../Style/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarządzanie naprawami</title>
</head>
<body>
<div id="holder">
    <header><a href="admin_panel.php" class="button">Powrót do panelu administracyjnego</a></header>
    <header><h2>Naprawy</h2></header>
    <div id="body">
        <div class="sekcja">
            <h3>Dodaj naprawę</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="iddostawcy_naprawa">Wybierz dostawcę:</label><br>
                    <select id="iddostawcy_naprawa" name="iddostawcy_naprawa">
                        <?php
                        mysqli_data_seek($result_dostawcy_naprawa,0);
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
        <div class="sekcja">
            <h3>Dzierżawa</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Dostawca</th>
                    <th>Nowy Dostawca</th>
                    <th>Kod sklepu</th>
                    <th>Nowy Kod sklepu</th>
                    <th>Kwota naprawy</th>
                    <th>Nowa Kwota naprawy</th>
                    <th>Data naprawy</th>
                    <th>Nowa Data naprawy</th>
                    <th>Faktura</th>
                    <th>Nowa Faktura</th>
                    <th>Edycja</th>
                    <th>Usuwanie</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result_naprawa_select)): ?>
                    <tr>
                        <td><?php echo $row['IDNaprawy']; ?></td>
                        <td><?php echo $row['NazwaDostawcy']; ?></td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <td>
                            <input type="hidden" name="idnaprawy" value="<?php echo $row['IDNaprawy']; ?>">
                            <select id="new_iddostawcy_naprawa" name="new_iddostawcy_naprawa">
                                <?php
                                mysqli_data_seek($result_dostawcy_naprawa, 0);
                                while ($row_dostawcy_naprawa = mysqli_fetch_assoc($result_dostawcy_naprawa)) {
                                    echo "<option value='" . $row_dostawcy_naprawa['IDDostawcy'] . "'>" . $row_dostawcy_naprawa['Nazwa'] . "</option>";
                                }
                                ?>
                            </select><br>
                        </td>
                        <td><?php echo $row['Lokalizacja']; ?></td>
                        <td>
                                <input type="hidden" name="idnaprawy" value="<?php echo $row['IDNaprawy']; ?>">
                                <input type="text" name="new_kod_sklepu_naprawa" value="<?php echo $row['KodSklepu']; ?>" >
                        </td>
                        <td><?php echo $row['Kwota']; ?></td>
                        <td>
                                <input type="hidden" name="idnaprawy" value="<?php echo $row['IDNaprawy']; ?>">
                                <input type="text" name="new_kwota_naprawy" value="<?php echo $row['Kwota']; ?>" required>
                        </td>
                            <td><?php echo $row['DataNaprawy']; ?></td>
                            <td>
                                <input type="hidden" name="idnaprawy" value="<?php echo $row['IDNaprawy']; ?>">
                                <input type="text" name="new_data_naprawy" value="<?php echo $row['DataNaprawy']; ?>" required>
                            </td>
                            <td><?php echo $row['numer_faktury']; ?></td>
                            <td>
                                <input type="hidden" name="idnaprawy" value="<?php echo $row['IDNaprawy']; ?>">
                                <input type="text" name="new_numer_faktury_naprawa" value="<?php echo $row['numer_faktury']; ?>" required>
                        </td>
                        <td>
                            <!-- Przycisk do zatwierdzenia zmian -->
                                <input type="hidden" name="idnaprawy" value="<?php echo $row['IDNaprawy']; ?>">
                                <input type="submit" name="edit_naprawa" value="Zapisz zmiany">
                        </td>
                        <td>
                            <!-- Formularz dla usuwania użytkownika -->
                                <input type="hidden" name="idnaprawy" value="<?php echo $row['IDNaprawy']; ?>">
                                <input type="submit" name="delete_naprawa" value="Usuń">
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
