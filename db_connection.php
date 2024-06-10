<?php
// db_connection.php
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$id_souteze = isset($_GET['id_souteze']) ? intval($_GET['id_souteze']) : 0;

if ($id_souteze == 0) {
    echo "ID_souteze není nastaveno.";
    exit;
}

// Database connection using PDO
$host = "md66.wedos.net";
$db_name = "d230417_buffit";
$username = "a230417_buffit";
$password = "495804Patrik.";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Get uploaded videos from the database
$videoQuery = "SELECT ID_videa, Title, Video_path FROM Soutezni_videa WHERE ID_souteze = :id_souteze";
$videoStmt = $conn->prepare($videoQuery);
$videoStmt->bindParam(':id_souteze', $id_souteze, PDO::PARAM_INT);
$videoStmt->execute();
$videos = $videoStmt->fetchAll(PDO::FETCH_ASSOC);
?>
