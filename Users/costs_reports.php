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
$ekko = 1;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report'])) {
    // Pobierz dane z formularza
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $selected_shops = $_POST["selected_shops"];
    $selected_suppliers = $_POST["selected_suppliers"];
    $start_year_month = date('Y-m', strtotime($start_date));
    $end_year_month = date('Y-m', strtotime($end_date));

    $_SESSION['start_year_month'] = $start_year_month;
    $_SESSION['end_year_month'] = $end_year_month;
    $_SESSION['selected_shops'] = $selected_shops;
    $_SESSION['selected_suppliers'] = $selected_suppliers;

    $lokalizacja_selected_shops_condition = !empty($selected_shops) ? "AND l.IDLokalizacji IN ('" . implode("','", $selected_shops) . "')" : "";
    $naprawy_selected_shops_condition = !empty($selected_shops) ? "AND np.IDLokalizacji IN ('" . implode("','", $selected_shops) . "')" : "";
    $tonery_selected_shops_condition = !empty($selected_shops) ? "AND tn.IDLokalizacji IN ('" . implode("','", $selected_shops) . "')" : "";

    $dzierzawa_selected_suppliers_condition = !empty($selected_suppliers) ? "AND dz.IDDostawcy IN ('" . implode("','", $selected_suppliers) . "')" : "";
    $naprawy_selected_suppliers_condition = !empty($selected_suppliers) ? "AND np.IDDostawcy IN ('" . implode("','", $selected_suppliers) . "')" : "";



    // Zapytanie SQL z warunkami
    $dzierzawa_sql = "SELECT 
                        l.IDLokalizacji, dz.KwotaJedNetto, dz.Ilosc, dz.StanNaDzisiaj, dz.KwotaDzierzawy, dz.Suma, DATE_FORMAT(dz.Data,'%Y-%m') AS DataDzierzawy, 
                        CONCAT(dm.Producent, ' ', dm.Model) AS ModelDrukarki, do.Nazwa, f.numer_faktury, CONCAT(l.Kod, ' - ', l.Nazwa_Lokalizacji) AS Lokalizacja
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
                    WHERE 
                        DATE_FORMAT(dz.Data,'%Y-%m') BETWEEN '$start_year_month' AND '$end_year_month'
                        $lokalizacja_selected_shops_condition
                        $dzierzawa_selected_suppliers_condition
                    ORDER BY DataDzierzawy ASC, l.IDLokalizacji ASC, di.IDDostawcy ASC";

    $naprawy_sql = "SELECT 
                        do.Nazwa, CONCAT(l.Kod, ' - ', l.Nazwa_Lokalizacji) AS Lokalizacja, np.Kwota, f.numer_faktury, DATE_FORMAT(np.DataNaprawy,'%Y-%m') AS DataNaprawy
                    FROM 
                        Naprawy np
                    JOIN 
                        Dostawca do ON np.IDDostawcy = do.IDDostawcy
                    JOIN
                        Lokalizacja l ON np.IDLokalizacji = l.IDLokalizacji
                    JOIN
                        Faktura f ON np.IDFaktury = f.id
                    WHERE 
                        DATE_FORMAT(np.DataNaprawy,'%Y-%m') BETWEEN '$start_year_month' AND '$end_year_month'
                        $naprawy_selected_shops_condition
                        $naprawy_selected_suppliers_condition
                    ORDER BY DataNaprawy ASC, np.IDLokalizacji ASC";

    $tonery_sql = "SELECT 
                        tn.Kwota, tn.Ilosc, tn.Suma, f.numer_faktury, DATE_FORMAT(tn.Data,'%Y-%m') AS DataTonerow, CONCAT(l.Kod, ' - ', l.Nazwa_Lokalizacji) AS Lokalizacja
                    FROM 
                        Tonery tn
                    JOIN
                        Lokalizacja l ON tn.IDLokalizacji = l.IDLokalizacji
                    JOIN
                        Faktura f ON tn.IDFaktury = f.id
                    WHERE 
                        DATE_FORMAT(tn.Data,'%Y-%m') BETWEEN '$start_year_month' AND '$end_year_month'
                        $tonery_selected_shops_condition
                    ORDER BY tn.Data ASC, tn.IDLokalizacji ASC";

    if(!in_array(1, $selected_suppliers)){
        $tonery_sql = "SELECT * FROM Tonery WHERE IDLokalizacji = 0";
    }

    $podsumowanie_sql = "SELECT 
                            CONCAT(l.Kod, ' - ', l.Nazwa_Lokalizacji) AS Lokalizacja,
                            COALESCE(SUM(tn.Suma), 0) AS SumaTonerow, 
                            COALESCE(SUM(np.Kwota), 0) AS SumaNapraw, 
                            COALESCE(SUM(dz.Suma), 0) AS SumaKosztowDzierzawy, 
                            DATE_FORMAT(dz.Data,'%Y-%m') AS DataDzierzaw, 
                            SUM(COALESCE(tn.Suma, 0) + COALESCE(np.Kwota, 0) + COALESCE(dz.Suma, 0)) AS SumaWszystkiego
                        FROM 
                            Lokalizacja l
                        LEFT JOIN
                            Tonery tn ON tn.IDLokalizacji = l.IDLokalizacji
                        LEFT JOIN
                            Naprawy np ON np.IDLokalizacji = l.IDLokalizacji
                        LEFT JOIN
                            DrukarkiInwentaryzacja di ON di.IDLokalizacji = l.IDLokalizacji
                        LEFT JOIN
                            Dzierzawa dz ON di.IDDrukarki = dz.IDDrukarki
                        WHERE 
                            DATE_FORMAT(dz.Data,'%Y-%m') BETWEEN '$start_year_month' AND '$end_year_month'
                            $lokalizacja_selected_shops_condition
                            $dzierzawa_selected_suppliers_condition
                        GROUP BY
                            l.IDLokalizacji, DataDzierzaw
                        ORDER BY
                            DataDzierzaw, Lokalizacja";
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporty Kosztów</title>
    <link rel="stylesheet" href="../Style/styles.css">
</head>
<body>
<div id="holder">
    <header>
        <a href="<?php echo $panel ?>" class="button">Powrót do panelu administracyjnego</a>
    </header>
    <header>
        <h2>Raporty Kosztów</h2>
    </header>
    <div id="body">
        <div id="sekcja">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="start_date">Data początkowa:</label>
                <input type="date" id="start_date" name="start_date" value="1980-01-01" required>
                <label for="end_date">Data końcowa:</label>
                <input type="date" id="end_date" name="end_date" value="2050-12-31" required><br>
                <label for="selected_shops">Wybierz sklepy:</label>
                <select id="selected_shops" name="selected_shops[]" multiple required>
                    <?php
                    $sql = "SELECT IDLokalizacji, CONCAT(Kod, ' - ', Nazwa_Lokalizacji) AS Nazwa FROM Lokalizacja";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["IDLokalizacji"] . "' selected='selected'>" . $row["Nazwa"] . "</option>";
                        }
                    } else {
                        echo "<option value='-1'>Brak sklepów</option>";
                    }
                    ?>
                </select><br>
                <label for="selected_suppliers">Wybierz dostawców:</label>
                <select id="selected_suppliers" name="selected_suppliers[]" multiple required>
                    <?php
                    $sql = "SELECT IDDostawcy, Nazwa FROM Dostawca";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["IDDostawcy"] . "' selected='selected'>" . $row["Nazwa"] . "</option>";
                        }
                    } else {
                        echo "<option value='-1'>Brak dostawców</option>";
                    }
                    ?>
                </select><br>
                <input type="submit" name="generate_report" value="Generuj raport">
            </form>
        </div>
        <div id="sekcja">
            <a class="button" href="../Config/generate_excel.php">Generuj plik Excel</a>
        </div>
        <div id="sekcja">
            <h1>Dzierżawa</h1>
            <div class="scrollable-section">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $result = $conn->query($dzierzawa_sql);
                    if ($result->num_rows > 0) {
                        echo "<table><tr><th>Data</th><th>Lokalizacja</th><th>Dostawca</th><th>Model Drukarki</th><th>Kwota Jednostkowa Netto</th><th>Ilość</th><th>Kwota Dzierżawy</th><th>Stan na dzisiaj</th><th>Suma</th><th>Numer Faktury</th></tr>";
                        while($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row["DataDzierzawy"] . "</td><td>" . $row["Lokalizacja"] . "</td><td>" . $row["Nazwa"] . "</td><td>" . $row["ModelDrukarki"] . "</td><td>" . $row["KwotaJedNetto"] . "</td><td>" . $row["Ilosc"] . "</td><td>" . $row["KwotaDzierzawy"] . "</td><td>" . $row["StanNaDzisiaj"] . "</td><td>" . $row["Suma"] . "</td><td>" . $row["numer_faktury"] . "</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "Brak danych do wyświetlenia";
                    }
                }
                ?>
            </div>
        </div>
        <div id="sekcja">
            <h1>Naprawy</h1>
            <div class="scrollable-section">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $result = $conn->query($naprawy_sql);
                    if ($result->num_rows > 0) {
                        echo "<table><tr><th>Data</th><th>Lokalizacja</th><th>Dostawca</th><th>Kwota Naprawy</th><th>Numer faktury</th></tr>";
                        while($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row["DataNaprawy"] . "</td><td>" . $row["Lokalizacja"] . "</td><td>" . $row["Nazwa"] . "</td><td>" . $row["Kwota"] . "</td><td>" . $row["numer_faktury"] . "</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "Brak danych do wyświetlenia";
                    }
                }
                ?>
            </div>
        </div>
        <div id="sekcja">
            <h1>Tonery</h1>
            <div class="scrollable-section">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $result = $conn->query($tonery_sql);
                    if ($result->num_rows > 0) {
                        echo "<table><tr><th>Data</th><th>Lokalizacja</th><th>Kwota</th><th>Ilosc</th><th>Suma</th><th>Numer faktury</th></tr>";
                        while($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row["DataTonerow"] . "</td><td>" . $row["Lokalizacja"] . "</td><td>" . $row["Kwota"] . "</td><td>" . $row["Ilosc"] . "</td><td>" . $row["Suma"] . "</td><td>" . $row["numer_faktury"] . "</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "Brak danych do wyświetlenia";
                    }
                }
                ?>
            </div>
        </div>
        <div id="sekcja">
            <h1>Podsumowanie</h1>
            <div class="scrollable-section">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $result = $conn->query($podsumowanie_sql);
                    if ($result->num_rows > 0) {
                        echo "<table><tr><th>Data</th><th>Lokalizacja</th><th>Suma Tonerow</th><th>Suma Napraw</th><th>Suma Kosztow Dzierżaw</th><th>Łączna suma kosztów</th></tr>";
                        while($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row["DataDzierzaw"] . "</td><td>" . $row["Lokalizacja"] . "</td><td>" . $row["SumaTonerow"] . "</td><td>" . $row["SumaNapraw"] . "</td><td>" . $row["SumaKosztowDzierzawy"] . "</td><td>" . $row["SumaWszystkiego"] . "</td></tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "Brak danych do wyświetlenia";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>

