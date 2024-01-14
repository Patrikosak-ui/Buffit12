<?php
session_start();


// Kontrola přihlášení
if (!isset($_SESSION['user_id'])) {
    // Uživatel není přihlášen, přesměrovat na přihlašovací stránku
    header("Location: login.php");
    exit();
}

$targetDirectory = "Videa/";
$uploadOk = 1;

// Zkontrolujte, zda byl formulář odeslán a byl vybrán soubor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
    $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
    $allowedFormats = array("mp4", "avi", "wmv", "mov");

    // Kontrola povolených formátů souborů
    if (!in_array($fileType, $allowedFormats)) {
        echo "Omlouváme se, povoleny jsou pouze soubory typu MP4, AVI, WMV a MOV.";
        $uploadOk = 0;
    }

    // Kontrola velikosti nahrávaného souboru
    if ($_FILES["fileToUpload"]["size"] > 50000000) { 
        echo "Omlouváme se, váš soubor je příliš velký.";
        $uploadOk = 0;
    }

    // Pokud nahrávání není v pořádku, zobrazte chybové hlášení
    if ($uploadOk == 0) {
        echo "Omlouváme se, váš soubor nebyl nahrán.";
    } else {
        $targetFile = $targetDirectory . basename($_FILES["fileToUpload"]["name"]);

        // Pokud byl soubor úspěšně nahrán
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo "Soubor " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " byl úspěšně nahrán.";

            $host = "md66.wedos.net";
            $db_name = "d230417_buffit";
            $username = "a230417_buffit";
            $password = "n6T3uSvj";

            try {
                $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : "Vložte zde nějaký titul";

                // Připravený dotaz pro vložení do databáze
                $sql = "INSERT INTO Soutezni_videa (Title, Video_path) VALUES (:title, :targetFile)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':targetFile', $targetFile);

                // Proveďte dotaz a zkontrolujte, zda byl úspěšně proveden
                if ($stmt->execute()) {
                    echo "Záznam byl úspěšně přidán do databáze.";
                } else {
                    echo "Chyba při přidávání záznamu do databáze.";
                }

                // Přesměrujte na soutezni_videa.php
                echo "<script>window.location.href = 'soutezni_videa.php';</script>";
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
    <title>Video Upload Form</title>
    <style>
        body {
            background-color: #494c4e;
            font-family: 'Arial', sans-serif;
            color: #fff;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        main {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            max-height: 600px;
            height: 100%;
        }

        h2 {
            text-align: center;
            color: #494c4e;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label, input {
            margin-bottom: 15px;
        }

        input[type="file"], input[type="text"], input[type="submit"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #494c4e;
            color: #fff;
        }

        input[type="submit"] {
            background-color: #494c4e;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #303336;
        }

        @media screen and (max-width: 600px) {
            main {
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <main>
        <h2>Video Upload Form</h2>
        <form action="upload_videa.php" method="post" enctype="multipart/form-data">
            <label for="fileToUpload">Select Video File:</label>
            <input type="file" name="fileToUpload" id="fileToUpload">
            
            <label for="title">Title:</label>
            <input type="text" name="title" id="title">

            <input type="submit" value="Upload Video" name="submit">
        </form>
    </main>
</body>
</html>
