<?php

$servername = "localhost";
$username = "uzivatel";
$password = "heslo";
$dbname = "buffit";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kontrola připojení
if ($conn->connect_error) {
    die("Připojení k databázi selhalo: " . $conn->connect_error);
}
?>