<?php
// Połączenie z bazą danych
require_once('../Config/db_connection.php');

// Otwarcie pliku CSV do odczytu
$csv_file = "csv.csv";

$file = fopen($csv_file, "r");

// Pomijanie pierwszego wiersza, który zazwyczaj zawiera nagłówki kolumn
fgetcsv($file);

// Odczytywanie danych z pliku CSV i wprowadzanie ich do bazy danych
while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
    $query = "SELECT dz.IDDrukarki 
    FROM Dzierzawa dz 
    JOIN DrukarkiInwentaryzacja di ON dz.IDDrukarki = di.IDDrukarki
    JOIN DrukarkiModele dm ON dm.IDDrukarki = di.IDModeluDrukarki
    JOIN Lokalizacja l ON di.IDLokalizacji = l.IDLokalizacji
    WHERE CONCAT(dm.Producent,' ',dm.Model) = '$data[0]' 
    AND l.Kod = '$data[1]'";

    // Przygotowanie instrukcji SQL
    $stmt = $conn->prepare($query);
    
    // Wykonanie zapytania
    $stmt->execute();
    
    // Pobranie wyniku zapytania
    $result = $stmt->get_result();
    
    // Sprawdzenie, czy zwrócono wyniki
    if ($result->num_rows > 0) {
        // Pobranie ID
        $row = $result->fetch_assoc();
        $id_drukarki = $row['IDDrukarki'];
        file_put_contents('debug.txt', $id_drukarki);
        
        // Wstawienie danych do bazy danych
        $sql = "INSERT INTO Dzierzawa (IDDrukarki, ...) VALUES (?, ...)";
        // Uzupełnij pozostałe kolumny i ich wartości w zapytaniu INSERT
        
        // Przygotowanie instrukcji SQL
        $stmt_insert = $conn->prepare($sql);
        
        // Przypisanie wartości do parametrów zapytania
        $stmt_insert->bind_param("s", $id_drukarki); // Tutaj należy uzupełnić pozostałe parametry
        
        // Wykonanie zapytania
        $stmt_insert->execute();
        
        // Sprawdzenie, czy wstawienie się powiodło
        if ($stmt_insert->affected_rows > 0) {
            echo "Rekord dodany pomyślnie<br>";
        } else {
            echo "Błąd podczas dodawania rekordu<br>";
        }
        
        // Zamknięcie instrukcji
        $stmt_insert->close();
    } else {
        echo "Nie znaleziono ID dla danych: {$data[0]}, {$data[1]}<br>";
    }
    
    // Zamknięcie instrukcji
    $stmt->close();
}

// Zamknięcie pliku CSV
fclose($file);
?>
