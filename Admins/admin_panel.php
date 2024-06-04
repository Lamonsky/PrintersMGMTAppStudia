<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany jako admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
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
        <header><a href="../Users/logout.php" class="button">Wyloguj</a></header>
        <header><h2>Panel Administracyjny</h2></header>
        <div id="body">
            <div class="sekcja">
                <ul>
                    <li><h3><a href="../Admins/admin_users_mgmt.php" class="button">Zarządzaj użytkownikami</a></h3></li>
                    <li><h3><a href="../Admins/admin_printers_mgmt.php" class="button">Zarządzaj drukarkami</a></h3></li>
                    <li><h3><a href="../Admins/admin_suppliers_shops_mgmt.php" class="button">Zarządzaj dostawcami/sklepami</a></h3></li>
                    <li><h3><a href="../Admins/admin_costs_mgmt.php" class="button">Zarządzaj kosztami</a></h3></li>
                    <li><h3><a href="../Users/costs_reports.php" class="button">Raport kosztów</a></h3></li>
                    <li><h3><a href="../Admins/dzierzawa.php" class="button">Zarządzaj dzierżawami</a></h3></li>
                    <li><h3><a href="../Admins/naprawy.php" class="button">Zarządzaj naprawami</a></h3></li>
                    <li><h3><a href="../Admins/tonery.php" class="button">Zarządzaj tonerami</a></h3></li>
                    <li><h3><a href="../Admins/faktura.php" class="button">Zarządzaj fakturami</a></h3></li>
                    <li><h3><a href="../Users/import.php" class="button">Import pliku excel dzierżawa</a></h3></li>
                    <li><h3><a href="../Users/import_naprawy.php" class="button">Import pliku excel naprawa</a></h3></li>
                    <li><h3><a href="../Users/import_tonery.php" class="button">Import pliku excel tonery</a></h3></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
