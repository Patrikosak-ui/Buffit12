<?php
session_start();

// Kontrola přihlášení
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_config.php'; 

function connectToDatabase($host, $db_name, $username, $password) {
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Funkce pro získání videí uživatele
function getUserVideos($userId, $conn) {
    $videosQuery = "SELECT * FROM Soutezni_videa WHERE ID_user = ?";
    $videosStmt = $conn->prepare($videosQuery);
    $videosStmt->execute([$userId]);
    $videos = $videosStmt->fetchAll(PDO::FETCH_ASSOC);
    return $videos;
}

// Funkce pro získání průměrného hodnocení uživatele
function getUserAverageRating($userId, $conn) {
    $ratingQuery = "SELECT AVG(Pocet_hvezd) AS average_rating FROM Hodnoceni WHERE ID_user = ?";
    $ratingStmt = $conn->prepare($ratingQuery);
    $ratingStmt->execute([$userId]);
    $result = $ratingStmt->fetch(PDO::FETCH_ASSOC);
    return $result['average_rating'];
}

// Připojení k databázi
$conn = connectToDatabase($dbConfig['host'], $dbConfig['db_name'], $dbConfig['username'], $dbConfig['password']);

$userId = $_SESSION['user_id'];
$profileQuery = "SELECT * FROM profiles WHERE user_id = ?";
$profileStmt = $conn->prepare($profileQuery);
$profileStmt->execute([$userId]);
$profile = $profileStmt->fetch(PDO::FETCH_ASSOC);

// Zpracování formuláře pro úpravu profilu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $instagram = $_POST['instagram'];
    $youtube = $_POST['youtube'];
    $instagram_odkaz = $_POST['instagram_odkaz'];
    $youtube_odkaz = $_POST['youtube_odkaz'];

    // Uložení nahraného profilového obrázku
    if (!empty($_FILES['profile_image']['tmp_name'])) {
        $profileImage = 'profile/' . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profileImage);
    } else {
        $profileImage = isset($profile['profile_image']) ? $profile['profile_image'] : '';
    }

    if ($profile) {
        // Profil existuje, aktualizujeme jej
        $updateQuery = "UPDATE profiles SET name = ?, age = ?, instagram = ?, youtube = ?, instagram_odkaz = ?, youtube_odkaz = ?, profile_image = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$name, $age, $instagram, $youtube, $instagram_odkaz, $youtube_odkaz, $profileImage, $userId]);
    } else {
        // Profil neexistuje, vytvoříme nový
        $insertQuery = "INSERT INTO profiles (user_id, name, age, instagram, youtube, instagram_odkaz, youtube_odkaz, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->execute([$userId, $name, $age, $instagram, $youtube, $instagram_odkaz, $youtube_odkaz, $profileImage]);
    }

    // Aktualizace profilových informací
    header("Location: profil.php");
    exit();
}

// Načtení videí uživatele
$userVideos = getUserVideos($userId, $conn);

// Získání průměrného hodnocení uživatele
$averageRating = getUserAverageRating($userId, $conn);

$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        /* Styl pro profilový obrázek v kolečku */
        .profile-picture-container {
            width: 150px;
            height: 150px;
            border-radius: 50%; /* aby byl obrázek v kruhu */
            overflow: hidden;
            margin-bottom: 20px;
        }

        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%; /* aby byl obrázek v kruhu */
        }

        .video {
            margin-bottom: 20px;
        }

        /* Upravený layout pro vedlejší zobrazení informací a videí */
        .profile-info-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .profile-info {
            flex: 1 1 300px;
            margin-right: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .user-videos {
            flex: 1 1 300px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <!-- Zobrazení profilových informací a videí -->
    <h1 class="mb-4">Tvůj Profil</h1>

    <div class="profile-info-container">
        <!-- Profilové informace -->
        <div class="profile-info">
            <?php
            if (isset($profile['profile_image'])) {
                echo '<div class="profile-picture-container">';
                echo '<img src="' . $profile['profile_image'] . '" class="profile-picture" alt="Profile Image">';
                echo '</div>';
            }

            echo '<p><strong>Jméno:</strong> ' . (isset($profile['name']) ? $profile['name'] : '') . '</p>';
            echo '<p><strong>Věk:</strong> ' . (isset($profile['age']) ? $profile['age'] : '') . '</p>';

            if (isset($profile['instagram']) && !empty($profile['instagram'])) {
                echo '<p><strong>Instagram:</strong> ' . $profile['instagram'];
                if (isset($profile['instagram_odkaz']) && !empty($profile['instagram_odkaz'])) {
                    echo ' <a href="' . $profile['instagram_odkaz'] . '" target="_blank"><img src="instagram_icon.png" alt="Instagram" style="width: 24px; height: 24px; margin-left: 10px;"></a>';
                }
                echo '</p>';
            }

            if (isset($profile['youtube']) && !empty($profile['youtube'])) {
                echo '<p><strong>YouTube:</strong> ' . $profile['youtube'];
                if (isset($profile['youtube_odkaz']) && !empty($profile['youtube_odkaz'])) {
                    echo ' <a href="' . $profile['youtube_odkaz'] . '" target="_blank"><img src="youtube_icon.png" alt="YouTube" style="width: 24px; height: 24px; margin-left: 10px;"></a>';
                }
                echo '</p>';
            }
            ?>

            <p><strong>Průměrné hodnocení:</strong> <?php echo isset($averageRating) ? number_format($averageRating, 2) : 'Není dostupné'; ?></p>
            <a href="#" id="edit-profile-btn" class="btn btn-secondary">Upravit profil</a>
        </div>

        <!-- Zobrazení nahraných videí -->
        <div class="user-videos">
            <h2>Nahraná videa:</h2>
            <?php
            if ($userVideos) {
                $videoCount = 0;
                foreach ($userVideos as $video) {
                    if ($videoCount < 2) { // Změněno na zobrazení pouze dvou videí
                        echo '<div class="video">';
                        echo '<h3>' . $video['Title'] . '</h3>';
                        echo '<video width="320" height="240" controls>';
                        echo '<source src="' . $video['Video_path'] . '" type="video/mp4">';
                        echo 'Your browser does not support the video tag.';
                        echo '</video>';
                        echo '<a href="delete_video.php?video_id=' . $video['ID_videa'] . '" class="btn btn-danger btn-sm mt-2">Smazat video</a>'; // Upraveno z ID na ID_videa
                        echo '</div>';
                        $videoCount++;
                    } else {
                        break;
                    }
                }
                // Tlačítko pro načtení dalších videí
                if (count($userVideos) > 2) {
                    echo '<button onclick="loadMoreVideos()" class="btn btn-primary mt-3">Zobrazit více</button>';
                }
            } else {
                echo '<p>Žádná videa nebyla nalezena.</p>';
            }
            ?>
        </div>
    </div>

    <!-- Formulář pro editaci profilu -->
    <form method="post" enctype="multipart/form-data" style="display: none;">
        <div class="form-group">
            <label for="profile_image">Profilový obrázek:</label>
            <div class="profile-picture-container" onmouseover="showFolderIcon()" onmouseout="hideFolderIcon()" onclick="uploadNewImage()">
                
            </div>
            <input type="file" id="new_profile_image" class="form-control-file" name="profile_image" accept="profile/*" style="display: none;">
        </div>

        <div class="form-group">
            <label for="name">Jméno:</label>
            <input type="text" class="form-control" name="name" value="<?php echo isset($profile['name']) ? $profile['name'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="age">Věk:</label>
            <input type="number" class="form-control" name="age" value="<?php echo isset($profile['age']) ? $profile['age'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="instagram">Instagram jméno:</label>
            <input type="text" class="form-control" name="instagram" value="<?php echo isset($profile['instagram']) ? $profile['instagram'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="youtube">YouTube jméno:</label>
            <input type="text" class="form-control" name="youtube" value="<?php echo isset($profile['youtube']) ? $profile['youtube'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="instagram_odkaz">Odkaz na Instagram:</label>
            <input type="text" class="form-control" name="instagram_odkaz" value="<?php echo isset($profile['instagram_odkaz']) ? $profile['instagram_odkaz'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="youtube_odkaz">Odkaz na YouTube:</label>
            <input type="text" class="form-control" name="youtube_odkaz" value="<?php echo isset($profile['youtube_odkaz']) ? $profile['youtube_odkaz'] : ''; ?>">
        </div>

        <button type="submit" class="btn btn-primary" name="save_profile">Uložit profil</button>
    </form>

</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
    document.getElementById('edit-profile-btn').addEventListener('click', function() {
        // Skryjeme profilové informace a zobrazíme formulář pro úpravu profilu
        document.querySelector('.profile-info').style.display = 'none';
        document.querySelector('.user-videos').style.display = 'none';
        document.querySelector('form').style.display = 'block';
    });

    function showFolderIcon() {
        // Zobrazíme ikonu složky při najetí myší na profilový obrázek
        document.querySelector('.folder-icon').style.display = 'block';
    }

    function hideFolderIcon() {
        // Skryjeme ikonu složky při odjetí myší z profilového obrázku
        document.querySelector('.folder-icon').style.display = 'none';
    }

    function uploadNewImage() {
        // Spustíme dialog pro výběr nového profilového obrázku
        document.getElementById('new_profile_image').click();
    }

    function loadMoreVideos() {
        // Implementujte AJAX načítání dalších videí zde
    }
</script>
</body>
</html>
</html>
