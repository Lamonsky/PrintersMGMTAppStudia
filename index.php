<?php
// index.php

session_start();

// Sprawdź czy użytkownik jest już zalogowany
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    // Jeśli tak, przekieruj go do odpowiedniej strony
    if ($_SESSION['role'] === 'Admin') {
        header('Location: Admins/admin_panel.php');
    } else {
        header('Location: Users/user_panel.php');
    }
    exit();
}

// Jeśli użytkownik nie jest zalogowany, przekieruj go do formularza logowania
header('Location: Users/login.php');
exit();
?>
