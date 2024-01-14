<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition Videos</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .video-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .remove-btn {
            margin-top: 10px;
        }

        .back-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <?php
    // Připojení k databázi pomocí PDO
    $host = "md66.wedos.net";
    $db_name = "d230417_buffit";
    $username = "a230417_buffit";
    $password = "n6T3uSvj";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    // Získání nahraných videí z databáze
    $videoQuery = "SELECT ID_videa, Title, Video_path FROM Soutezni_videa";
    $videoStmt = $conn->query($videoQuery);
    $videos = $videoStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="container mt-5">
        <a href="index.html" class="btn btn-primary back-btn">Back to Index</a>
        <h1 class="mb-4">Soutezni Videa</h1>

        <?php
        if (!empty($videos)) {
            foreach ($videos as $video) {
                echo '<div class="video-container">';
                echo '<h3>' . htmlspecialchars($video['Title']) . '</h3>';
                echo '<video width="640" height="480" controls>';
                echo '<source src="' . $video['Video_path'] . '" type="video/mp4">';
                echo 'Your browser does not support the video tag.';
                echo '</video>';
                echo '<form method="post" onsubmit="return confirm(\'Are you sure you want to remove this video?\');">';
                echo '<input type="hidden" name="video_id" value="' . $video['ID_videa'] . '">';
                echo '<button type="submit" class="btn btn-danger remove-btn" name="remove_video">Remove</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo '<p>No videos available.</p>';
        }

        // Zpracování odstranění videa
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_video'])) {
            $videoId = $_POST['video_id'];
            $removeQuery = "DELETE FROM Soutezni_videa WHERE ID_videa = ?";
            $removeStmt = $conn->prepare($removeQuery);
            $removeStmt->execute([$videoId]);

            // Přesměrování na aktualizovanou stránku
            echo '<script>window.location.href = "soutezni_videa.php";</script>';
        }
        ?>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
