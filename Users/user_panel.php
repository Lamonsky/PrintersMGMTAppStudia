<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany jako admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Uzytkownik') {
    header('Location: ../Users/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny</title>
    <link rel="stylesheet" href="../Style/styles.css">
</head>
<body>
    <div id="holder">
        <header><a href="logout.php" class="button">Wyloguj</a></header>
        <header><h2>Panel Administracyjny</h2></header>
        <div id="body">
            <div class="sekcja">
                <ul>
                    <li><h3><a href="user_mgmt.php" class="button">Zarządzanie kontem</a></h3></li>
                    <li><h3><a href="costs_reports.php" class="button">Raport kosztów</a></h3></li>
                </ul>
            </div>
        </div>
        
    </div>
</body>
</html>
