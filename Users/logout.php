<?php
// logout.php

// Rozpocznij lub wznow sesję
session_start();

session_unset();
// Zniszcz wszystkie dane sesji
session_destroy();

// Przekieruj użytkownika do strony logowania
header('Location: login.php');
exit();
?>
