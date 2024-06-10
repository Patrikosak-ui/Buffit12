<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$targetDirectory = "Videa/";
$uploadOk = 1;

// Připojení k databázi pro získání soutěží
$host = "md66.wedos.net";
$db_name = "d230417_buffit";
$username = "a230417_buffit";
$password = "495804Patrik.";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Získání soutěží z tabulky Souteze
    $soutezQuery = "SELECT ID_souteze, event_name FROM Souteze";
    $soutezStmt = $conn->prepare($soutezQuery);
    $soutezStmt->execute();
    $souteze = $soutezStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Chyba připojení k databázi: " . $e->getMessage();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
    $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
    $allowedFormats = array("mp4", "avi", "wmv", "mov");

    if (!in_array($fileType, $allowedFormats)) {
        echo "Omlouváme se, povoleny jsou pouze soubory typu MP4, AVI, WMV a MOV.";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 50000000) {
        echo "Omlouváme se, váš soubor je příliš velký.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Soubor nebyl vybrán.";
    } else {
        $targetFile = $targetDirectory . basename($_FILES["fileToUpload"]["name"]);

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo "Soubor " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " byl úspěšně nahrán.";

            try {
                $title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : "Název Události";
                $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : "Vložte zde popis";
                $ID_souteze = isset($_POST['ID_souteze']) ? intval($_POST['ID_souteze']) : null;
                $ID_user = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

                $sql = "INSERT INTO Soutezni_videa (ID_user, ID_souteze, Title, Description, Video_path) VALUES (:ID_user, :ID_souteze, :title, :description, :targetFile)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':ID_user', $ID_user);
                $stmt->bindParam(':ID_souteze', $ID_souteze);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':targetFile', $targetFile);

                if ($stmt->execute()) {
                    echo "Záznam byl úspěšně přidán do databáze.";
                } else {
                    echo "Chyba při přidávání záznamu do databáze.";
                }

                echo "<script>window.location.href = 'index.php';</script>";
            } catch (PDOException $e) {
                echo "Chyba: " . $e->getMessage();
            }

            $conn = null;
        } else {
            echo "Omlouváme se, došlo k chybě při nahrávání souboru.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form pro nahrávání videí</title>
    <link rel="stylesheet" href="upload_videa.css">
</head>
<body>
    <main class="wrapper">
        <div class="logo">
            <img src="logo_white2.png" alt="Logo">
        </div>
        <h2 class="name">Přihlášení do Soutěže</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-field">
                <label for="fileToUpload">Vyberte Soutěžní Video:</label>
                <input type="file" name="fileToUpload" id="fileToUpload">
            </div>
            <div class="form-field">
                <label for="ID_souteze">Vyberte Soutěž:</label>
                <select name="ID_souteze" id="ID_souteze">
                    <?php foreach ($souteze as $soutez): ?>
                        <option value="<?php echo $soutez['ID_souteze']; ?>"><?php echo htmlspecialchars($soutez['event_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-field">
                <label for="title">Název:</label>
                <input type="text" name="title" id="title">
            </div>
            <div class="form-field">
                <label for="description">Popis:</label>
                <textarea name="description" id="description"></textarea> <!-- Změna na textové pole -->
            </div>
            <input type="submit" value="Nahrát Video" name="submit" class="btn">
        </form>
    </main>
</body>
</html>
