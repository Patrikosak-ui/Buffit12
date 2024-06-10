<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_config.php';

function connectToDatabase($host, $db_name, $username, $password) {
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'msg' => 'Chyba připojení k databázi: ' . $e->getMessage()]);
        exit();
    }
}

$conn = connectToDatabase($dbConfig['host'], $dbConfig['db_name'], $dbConfig['username'], $dbConfig['password']);

if (isset($_GET['video_id'])) {
    $videoId = $_GET['video_id'];
    $userId = $_SESSION['user_id'];

    // Zkontrolujeme, jestli video patří aktuálnímu uživateli
    $checkQuery = "SELECT * FROM Soutezni_videa WHERE ID_videa = ? AND ID_user = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([$videoId, $userId]);

    if ($checkStmt->rowCount() > 0) {
        // Smažeme všechny související záznamy z tabulky Hodnoceni
        $deleteRatingsQuery = "DELETE FROM Hodnoceni WHERE ID_videa = ?";
        $deleteRatingsStmt = $conn->prepare($deleteRatingsQuery);
        $deleteRatingsStmt->execute([$videoId]);

        // Smažeme video
        $deleteVideoQuery = "DELETE FROM Soutezni_videa WHERE ID_videa = ? AND ID_user = ?";
        $deleteVideoStmt = $conn->prepare($deleteVideoQuery);
        $deleteVideoStmt->execute([$videoId, $userId]);

        header("Location: profil.php");
        exit();
    } else {
        echo "Video nenalezeno nebo nemáte oprávnění k jeho smazání.";
    }
} else {
    echo "Nesprávný požadavek.";
}

$conn = null;
?>
