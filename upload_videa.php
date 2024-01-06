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

        form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #494c4e;
            color: #fff;
        }

        form input[type="text"] {
            background-color: #494c4e;
            color: #fff;
        }

        form input[type="submit"] {
            background-color: #494c4e;
            color: #fff;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
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

<?php
$targetDirectory = "C:/xampp/htdocs/buffit/Videa/";
$uploadOk = 1;

// Check if the file is a video file
if (isset($_POST["submit"]) && isset($_FILES["fileToUpload"])) {
    $fileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
    $allowedFormats = array("mp4", "avi", "wmv", "mov");

    if (!in_array($fileType, $allowedFormats)) {
        echo "Sorry, only MP4, AVI, WMV, and MOV files are allowed.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 50000000) { // Adjust the file size limit as needed
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        $targetFile = $targetDirectory . basename($_FILES["fileToUpload"]["name"]);

        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";

        
            $host = "MariaDB";
            $db_name = "d230417_rofl";
            $username = "a230417_buffit";
            $password = "n6T3uSvj";

            try {
                $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Získání titulu z formuláře
                $title = isset($_POST['title']) ? $_POST['title'] : "Vložte zde nějaký titul";

                // Příprava SQL dotazu pro vložení do tabulky
                $sql = "INSERT INTO soutezni_videa (title, video_path) VALUES (:title, :targetFile)";
                
                // Příprava a provedení připraveného dotazu
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':targetFile', $targetFile);
                $stmt->execute();

                echo "Record added to the database successfully.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }

            // Uzavření připojení k databázi
            $conn = null;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
