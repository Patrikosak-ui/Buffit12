<?php

if (isset($_GET['videoPath'])) {
    $videoPath = $_GET['videoPath'];

    $host = "md66.wedos.net";
    $db_name = "d230417_buffit";
    $username = "a230417_buffit";
    $password = "n6T3uSvj";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Získání titulu videa před jeho smazáním pro vytvoření informace pro uživatele
        $stmt_title = $conn->prepare("SELECT Title FROM Soutezni_videa WHERE Video_path = :videoPath");
        $stmt_title->bindParam(':videoPath', $videoPath);
        $stmt_title->execute();
        $videoTitle = $stmt_title->fetchColumn();

        // Smazání videa z databáze
        $stmt_delete = $conn->prepare("DELETE FROM Soutezni_videa WHERE Video_path = :videoPath");
        $stmt_delete->bindParam(':videoPath', $videoPath);
        $stmt_delete->execute();

        // Smazání souboru videa ze serveru
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }

        // Přesměrování s informací pro uživatele
        header("Location: soutezni_videa.php?deleted=true&title=" . urlencode($videoTitle));
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        // Ujistěte se, že vždy uzavřete spojení s databází
        $conn = null;
    }
}

?>
