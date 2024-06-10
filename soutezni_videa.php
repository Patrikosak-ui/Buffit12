<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soutěžní Videa</title>
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

        .stars span {
            cursor: pointer;
            font-size: 30px;
            color: lightgray;
        }

        .stars .rated {
            color: #FFD700;
        }

        .rating-message {
            margin-top: 10px;
            font-weight: bold;
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>
    <?php
    // Start session to access user ID
    session_start();

    // Připojení k databázi pomocí PDO
    $host = "md66.wedos.net";
    $db_name = "d230417_buffit";
    $username = "a230417_buffit";
    $password = "495804Patrik.";

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
                echo '<div class="stars" data-video-id="' . $video['ID_videa'] . '">';
                for ($i = 1; $i <= 5; $i++) {
                    echo '<span class="star" data-rating="' . $i . '">&#9733;</span>';
                }
                echo '</div>';
                echo '<div class="rating-message" data-video-id="' . $video['ID_videa'] . '"></div>';
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Přidání události kliknutí na hvězdičky
            document.querySelectorAll('.star').forEach(function(star) {
                star.addEventListener('click', function() {
                    var videoId = this.parentElement.getAttribute('data-video-id');
                    var rating = parseInt(this.getAttribute('data-rating'));
                    setRating(videoId, rating);
                });
            });

            // Funkce pro nastavení hodnocení hvězdiček
            function setRating(videoId, rating) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "handle_rating.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            document.querySelectorAll('.stars[data-video-id="' + videoId + '"] .star').forEach(function(star) {
                                if (parseInt(star.getAttribute('data-rating')) <= rating) {
                                    star.classList.add('rated');
                                } else {
                                    star.classList.remove('rated');
                                }
                            });
                            document.querySelector('.rating-message[data-video-id="' + videoId + '"]').textContent = 'Hodnocení bylo úspěšně uloženo.';
                        } else {
                            console.error("Chyba: " + response.message);
                        }
                    }
                };

                xhr.send("video_id=" + videoId + "&rating=" + rating);
            }
        });
    </script>
</body>
</html>
