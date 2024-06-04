<?php
// db_connection.php
$host = "localhost";
$username = "root";
$password = "lamon123";
$database = "PrintersDB";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Połączenie nieudane: " . mysqli_connect_error());
}
?>
