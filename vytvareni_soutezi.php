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

// Zpracování formuláře pro vytvoření soutěže
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_id'] == 8) {
    // Zpracování názvu soutěže
    $nazev_souteze = filter_input(INPUT_POST, 'nazev_souteze', FILTER_SANITIZE_STRING);

    // Zpracování datumu od a datumu do
    $datum_od = filter_input(INPUT_POST, 'datum_od', FILTER_SANITIZE_STRING);
    $datum_do = filter_input(INPUT_POST, 'datum_do', FILTER_SANITIZE_STRING);

    // Zpracování popisku
    $popisek = filter_input(INPUT_POST, 'popisek', FILTER_SANITIZE_STRING);

    // Zpracování nahrání videa
    if ($_FILES['video']['error'] === 0) {
        // Získejte unikátní název souboru pro video
        $videoName = uniqid('video_') . '.mp4';
        $targetDirectory = "soutez/";
        $targetFile = $targetDirectory . $videoName;

        if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFile)) {
            // Videa jsou úspěšně nahrána, uložení cesty do databáze
            $videoPath = $targetFile;

            // Připravený dotaz pro vložení soutěže do databáze s cestou k videu
            $sql = "INSERT INTO Souteze (nazev_souteze, datum_zacatku, datum_konce, popisek, video_path, ID_user) VALUES (:nazev_souteze, :datum_od, :datum_do, :popisek, :video_path, :ID_user)";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':nazev_souteze', $nazev_souteze);
            $stmt->bindParam(':datum_od', $datum_od);
            $stmt->bindParam(':datum_do', $datum_do);
            $stmt->bindParam(':popisek', $popisek);
            $stmt->bindParam(':video_path', $videoPath);
            $stmt->bindParam(':ID_user', $_SESSION['user_id']);

            // Proveďte dotaz a zkontrolujte, zda byl úspěšně proveden
            if ($stmt->execute()) {
                echo "Soutěž byla úspěšně vytvořena s videem.";
            }
        } else {
            echo "Omlouváme se, došlo k chybě při nahrávání videa. Chyba: " . $_FILES['video']['error'];
        }
    } else {
        echo "Videa nebyla nahrána.";
    }
}

// Získání seznamu soutěží majitele
$ID_user = $_SESSION['user_id'];
$soutezeQuery = "SELECT * FROM Souteze WHERE ID_user = :ID_user";
$soutezeStmt = $conn->prepare($soutezeQuery);
$soutezeStmt->bindParam(':ID_user', $ID_user);
$soutezeStmt->execute();
$souteze = $soutezeStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vytvoření soutěže</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Vytvoření soutěže</h1>

        <!-- Formulář pro vytvoření soutěže -->
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nazev_souteze">Název soutěže:</label>
                <input type="text" class="form-control" id="nazev_souteze" name="nazev_souteze" required>
            </div>
            <div class="form-group">
                <label for="datum_od">Datum začátku:</label>
                <input type="date" class="form-control" id="datum_od" name="datum_od" required>
            </div>
            <div class="form-group">
                <label for="datum_do">Datum konce:</label>
                <input type="date" class="form-control" id="datum_do" name="datum_do" required>
            </div>
            <div class="form-group">
                <label for="popisek">Popisek:</label>
                <textarea class="form-control" id="popisek" name="popisek" required></textarea>
            </div>
            <div class="form-group">
                <label for="video">Video soubor:</label>
                <input type="file" class="form-control-file" id="video" name="video" accept="video/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Vytvořit soutěž</button>
            <a href="index.html" class="btn btn-primary">Back to Index</a>
        </form>

        <hr>

        <!-- Zobrazení seznamu soutěží majitele -->
        <h2 class="mt-4">Seznam soutěží</h2>
        <?php
        // Zkontrolujte, zda máte soutěže v databázi
        if (!empty($souteze)) {
            echo "<ul>";
            foreach ($souteze as $soutez) {
                echo "<li>{$soutez['nazev_souteze']} ({$soutez['datum_zacatku']} - {$soutez['datum_konce']}) - {$soutez['popisek']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Žádné soutěže nebyly nalezeny.</p>";
        }
        ?>

        <!-- Přidání tlačítka pro přesměrování na seznam_soutezi.php -->
        <a href="seznam_soutezi.php" class="btn btn-primary">Zobrazit seznam soutěží</a>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
