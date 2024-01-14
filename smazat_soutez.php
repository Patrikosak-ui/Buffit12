<?php
session_start();

// Kontrola přihlášení
if (!isset($_SESSION['user_id'])) {
    // Uživatel není přihlášen, přesměrovat na přihlašovací stránku
    header("Location: login.php");
    exit();
}

// Kontrola oprávnění uživatele
if ($_SESSION['user_id'] != 8) {
    // Uživatel nemá oprávnění, přesměrovat na domovskou stránku nebo zobrazit chybu
    header("Location: index.php"); // Případně zobrazit chybovou hlášku nebo nic nedělat
    exit();
}

// Připojení k databázi pomocí PDO
$host = "md66.wedos.net";
$db_name = "d230417_buffit";
$username = "a230417_buffit";
$password = "n6T3uSvj";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Zpracování požadavku na smazání soutěže
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Zpracování ID soutěže k odstranění
    $soutez_id = $_POST['soutez_id'];

    // Získání cesty k videu před smazáním z databáze
    $videoPathQuery = "SELECT video_path FROM Souteze WHERE ID_souteze = :soutez_id AND ID_user = :ID_user";
    $videoPathStmt = $conn->prepare($videoPathQuery);
    $videoPathStmt->bindParam(':soutez_id', $soutez_id);
    $videoPathStmt->bindParam(':ID_user', $_SESSION['user_id']);
    $videoPathStmt->execute();
    $videoPath = $videoPathStmt->fetchColumn();

    // Připravený dotaz pro smazání soutěže z databáze
    $deleteSql = "DELETE FROM Souteze WHERE ID_souteze = :soutez_id AND ID_user = :ID_user";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bindParam(':soutez_id', $soutez_id);
    $deleteStmt->bindParam(':ID_user', $_SESSION['user_id']);

    // Proveďte dotaz a zkontrolujte, zda byl úspěšně proveden
    if ($deleteStmt->execute()) {
        // Úspěšné smazání - smažte také fyzický soubor videa
        $filePath = "soutez/" . basename($videoPath);
        
        // Zkontrolujte, zda soubor existuje před pokusem o jeho smazání
        if (file_exists($filePath) && unlink($filePath)) {
            // Soubor videa byl úspěšně smazán - přesměrování na seznam soutěží
            header("Location: seznam_soutezi.php");
            exit();
        } else {
            // Chyba při mazání souboru videa nebo soubor neexistuje
            echo "Omlouváme se, došlo k chybě při mazání souboru videa.";
        }
    } else {
        // Chyba při mazání soutěže z databáze
        echo "Omlouváme se, došlo k chybě při mazání soutěže. Chyba: " . implode(", ", $deleteStmt->errorInfo());
    }
}
?>
<!-- ... (zbytek kódu) -->
