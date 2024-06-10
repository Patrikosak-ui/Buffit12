<?php
// Display_video.php

session_start();
require_once 'db_config.php'; // Importujeme konfigurační soubor pro připojení k databázi

try {
    $conn = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['db_name']}", $dbConfig['username'], $dbConfig['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    exit;
}

// Získání videí z databáze
$videoQuery = "SELECT * FROM Video";
$videoStmt = $conn->query($videoQuery);
$videos = $videoStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($videos);
?>
