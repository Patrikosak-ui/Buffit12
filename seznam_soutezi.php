<?php
session_start();

// Kontrola přihlášení
if (!isset($_SESSION['user_id'])) {
    // Uživatel není přihlášen, přesměrovat na přihlašovací stránku
    header("Location: login.php");
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['smazat_soutez'])) {
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
            // Soubor videa byl úspěšně smazán - obnovte stránku pro zobrazení aktualizovaného seznamu soutěží
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

// Získání seznamu soutěží
$soutezeQuery = "SELECT * FROM Souteze";
$soutezeStmt = $conn->prepare($soutezeQuery);
$soutezeStmt->execute();
$souteze = $soutezeStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam soutěží</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Seznam soutěží</h1>

        <!-- Zobrazení seznamu soutěží -->
        <?php
        // Zkontrolujte, zda máte soutěže v databázi
        if (!empty($souteze)) {
            foreach ($souteze as $soutez) {
                echo "<div class='card mt-4'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>{$soutez['nazev_souteze']}</h5>";
                echo "<p class='card-text'>Datum začátku: {$soutez['datum_zacatku']}</p>";
                echo "<p class='card-text'>Datum konce: {$soutez['datum_konce']}</p>";
                echo "<p class='card-text'>Popisek: {$soutez['popisek']}</p>";
                echo "<video width='320' height='240' controls>";
                echo "<source src='{$soutez['video_path']}' type='video/mp4'>";
                echo "Your browser does not support the video tag.";
                echo "</video>";

                // Tlačítko pro smazání soutěže (pouze pro ID_user 8)
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 8) {
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='soutez_id' value='{$soutez['ID_souteze']}'>";
                    echo "<button type='submit' name='smazat_soutez' class='btn btn-danger'>Smazat soutěž</button>";
                    echo "</form>";
                }

                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>Žádné soutěže nebyly nalezeny.</p>";
        }
        ?>
    </div>
    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
