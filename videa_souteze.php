<?php
session_start();

$host = "md66.wedos.net";
$db_name = "d230417_buffit";
$username = "a230417_buffit";
$password = "495804Patrik.";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $ID_souteze = isset($_GET['ID_souteze']) ? intval($_GET['ID_souteze']) : null;

    if ($ID_souteze) {
        $videaQuery = "SELECT ID_videa, Title, Description, Video_path FROM Soutezni_videa WHERE ID_souteze = :ID_souteze";
        $videaStmt = $conn->prepare($videaQuery);
        $videaStmt->bindParam(':ID_souteze', $ID_souteze);
        $videaStmt->execute();
        $videa = $videaStmt->fetchAll(PDO::FETCH_ASSOC);

        $soutezQuery = "SELECT event_name FROM Souteze WHERE ID_souteze = :ID_souteze";
        $soutezStmt = $conn->prepare($soutezQuery);
        $soutezStmt->bindParam(':ID_souteze', $ID_souteze);
        $soutezStmt->execute();
        $soutez = $soutezStmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "Neplatný identifikátor soutěže.";
        exit();
    }
} catch (PDOException $e) {
    echo "Chyba připojení k databázi: " . $e->getMessage();
    exit();
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videa Soutěže</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }
        .star {
            cursor: pointer;
            color: #000; /* Orange star color */
            font-size: 32px; /* Larger star size */
        }
        .star.rated {
            color: #FFD700; /* Yellow when rated */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Videa pro soutěž: <?php echo htmlspecialchars($soutez['event_name']); ?></h1>
    <div class="row">
        <?php
        $videoCount = 0;
        if (!empty($videa)) {
            foreach ($videa as $video) {
                $hiddenClass = $videoCount >= 4 ? 'hidden' : '';
                echo '<div class="video-col ' . $hiddenClass . '" data-video-id="' . $video['ID_videa'] . '">';
                echo '<div class="video-container">';
                echo '<h3>' . htmlspecialchars($video['Title']) . '</h3>';
                echo '<p>' . htmlspecialchars($video['Description']) . '</p>';
                echo '<video width="100%" controls>';
                echo '<source src="' . $video['Video_path'] . '" type="video/mp4">';
                echo 'Váš prohlížeč nepodporuje video tag.';
                echo '</video>';
                echo '<div class="stars" data-video-id="' . $video['ID_videa'] . '">';
                for ($i = 1; $i <= 5; $i++) {
                    echo '<span class="star" data-rating="' . $i . '">&#9733;</span>';
                }
                echo '</div>';
                echo '<div class="rating-message" data-video-id="' . $video['ID_videa'] . '"></div>';
                echo '</div>';
                echo '</div>';
                $videoCount++;
            }
        } else {
            echo '<p>Žádná videa nejsou k dispozici.</p>';
        }
        ?>
    </div>
    <?php if ($videoCount > 4) { ?>
        <button id="show-more" class="btn btn-secondary">Zobrazit více</button>
    <?php } ?>

    <?php if ($videoCount > 4) { ?>
        <button id="show-less" class="btn btn-secondary" style="display:none">Zobrazit méně</button>
    <?php } ?>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Adding click event to stars
        document.querySelectorAll('.star').forEach(function(star) {
            star.addEventListener('click', function() {
                var videoId = this.parentElement.getAttribute('data-video-id');
                var rating = parseInt(this.getAttribute('data-rating'));
                setRating(videoId, rating);
            });
        });

        // Function to set star rating
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

        // Show more functionality
        document.getElementById('show-more').addEventListener('click', function() {
            document.querySelectorAll('.hidden').forEach(function(video) {
                video.classList.remove('hidden');
            });
            this.style.display = 'none';
            document.getElementById('show-less').style.display = 'block';
        });

        // Show less functionality
        document.getElementById('show-less').addEventListener('click', function() {
            document.querySelectorAll('.video-col:nth-child(n+5)').forEach(function(video) {
                video.classList.add('hidden');
            });
            this.style.display = 'none';
            document.getElementById('show-more').style.display = 'block';
        });
    });
</script>
</body>
</html>
