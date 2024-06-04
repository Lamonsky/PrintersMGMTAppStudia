<?php
session_start();
require_once('../Config/db_connection.php');

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

require '../vendor/autoload.php'; // Wymagane dla PhpSpreadsheet


use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['excel_file'])) {
    $file_name = $_FILES['excel_file']['name'];
    $file_tmp = $_FILES['excel_file']['tmp_name'];

    $nr_faktury = '"'.$_POST["nr_faktury"].'"';
    $sklep = 0;
    $drukarka = 0;
    $ilosc = intval(0);
    $kwota = floatval(0);
    $id_faktury = 0;
    $data_dzierzawa = date("Y-m-d", strtotime($_POST['data_dzierzawa']));
    //$data_dzierzawa = $_POST["data_dzierzawa"];
    $suma = floatval(0);

    // Sprawdzenie czy plik jest w formacie Excel (XLS lub XLSX)
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if (in_array($file_ext, array('xls', 'xlsx'))) {
        try {
            // Wczytanie pliku Excel
            $spreadsheet = IOFactory::load($file_tmp);
            $sheet = $spreadsheet->getActiveSheet();

            // Pobranie wszystkich danych z arkusza
            $data = $sheet->toArray();

            // Wyświetlenie danych jako tabela HTML
            foreach ($data as $rowIndex => $row) {
                foreach ($row as $columnIndex => $cell) {
                    // Wyświetlenie numeru kolumny
                    switch ($columnIndex) {
                        case 0:
							if ($cell == "HQ"){
								$drukarka = 88;
							}
							else{
								$sklep = $cell;
								$query_sklep = "SELECT IDdrukarki FROM drukarkiinwentaryzacja di INNER JOIN lokalizacja l 
										ON di.IDLokalizacji = l.IDLokalizacji WHERE l.Kod = '$sklep'";
								$result_sklep = mysqli_query($conn, $query_sklep);
								while($row = mysqli_fetch_assoc($result_sklep)) {
									$drukarka = $row['IDdrukarki'];
								}
							}
                            break;
                        case 1:
                            $ilosc = intval($cell);
                            break;
                        case 2:
							setlocale(LC_NUMERIC, 'en_US.UTF-8');
							$cell = str_replace(',', '.', $cell);
                            $kwota = floatval($cell);
                            break;
                    }
                }
				
                $query_faktura = "select id from faktura where numer_faktury = $nr_faktury";
                $result_faktura = mysqli_query($conn, $query_faktura);
                if($row = mysqli_fetch_assoc($result_faktura)){
                    $id_faktura = $row['id'];
                }
                $suma = $kwota * $ilosc;
                $query_insert = "INSERT INTO dzierzawa (IDFaktury, IDDrukarki, IDDostawcy, KwotaJedNetto, Ilosc, Data, StanNaDzisiaj, KwotaDzierzawy, Suma)
                                VALUES ($id_faktura, $drukarka, 1, $kwota, $ilosc, '$data_dzierzawa', 0, 0, $suma)";
                $result_insert = mysqli_query($conn, $query_insert);
                if($result_insert){
                    echo "Dane zostały dodane";
                    echo "<br>";
                }
                else{
                    "Błąd: " . mysqli_error($conn);
                }

            }
        } catch (Exception $e) {
            echo 'Wystąpił błąd podczas przetwarzania pliku: ' . $e->getMessage();
        }
    } else {
        echo "Niewłaściwy format pliku. Proszę przesłać plik Excel w formacie XLS lub XLSX.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Przesyłanie i wyświetlanie pliku Excel</title>
    <link rel="stylesheet" href="../Style/styles.css">
</head>
<body>
<header><a href="<?php echo $panel ?>" class="button">Powrót do panelu administracyjnego</a></header>
<h2>Przesyłanie pliku Excel</h2>
Plik excel ma mieć tylko 3 kolumny.<br>
Pierwsza z kodem sklepu<br>
Druga z ilością<br>
Trzecia z kwotą jednostkową netto<br>
Reszta jest wyliczana automatycznie<br><br><br>
<form action="" method="post" enctype="multipart/form-data">
    <label for="nrfaktury">Numer faktury:</label><br>
    <input type="text" id="nr_faktury" name="nr_faktury" required><br>
    <label for="data_dzierzawa">Data:</label><br>
    <input type="date" id="data_dzierzawa" name="data_dzierzawa" required pattern="dd/mm/yyyy"><br>
    Wybierz plik Excel do przesłania:
    <input type="file" name="excel_file" accept=".xlsx,.xls"><br>
    <input type="submit" value="Prześlij plik" name="submit">
</form>

</body>
</html>
