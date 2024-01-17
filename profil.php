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


function handleProfileImageUpload($file, $userId, $conn) {
    if ($file['error'] === 0) {
        $targetDirectory = "profile/";
        $targetFile = $targetDirectory . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $imagePath = $targetFile;
            $updateImageQuery = "UPDATE profiles SET profile_image = ? WHERE user_id = ?";
            $updateImageStmt = $conn->prepare($updateImageQuery);
            $updateImageStmt->execute([$imagePath, $userId]);
            echo "Profilový obrázek byl úspěšně nahrán.";
        } else {
            echo "Omlouváme se, došlo k chybě při nahrávání souboru.";
        }
    } else {
        echo "Omlouváme se, došlo k chybě při nahrávání souboru. Chyba: " . $file['error'];
    }
}

// Zpracování formuláře pro editaci
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $conn = connectToDatabase($dbConfig['host'], $dbConfig['db_name'], $dbConfig['username'], $dbConfig['password']);

    // Získání hodnot z formuláře
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
    $instagram = filter_input(INPUT_POST, 'instagram', FILTER_SANITIZE_STRING);
    $youtube = filter_input(INPUT_POST, 'youtube', FILTER_SANITIZE_STRING);

    // Uložení do databáze pro přihlášeného uživatele
    $userId = $_SESSION['user_id'];

    // Zjistit, zda uživatel již má vytvořený profil
    $checkProfileQuery = "SELECT * FROM profiles WHERE user_id = ?";
    $checkProfileStmt = $conn->prepare($checkProfileQuery);
    $checkProfileStmt->execute([$userId]);
    $existingProfile = $checkProfileStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingProfile) {
        // Profil již existuje, provedeme aktualizaci
        handleProfileImageUpload($_FILES['profile_image'], $userId, $conn);

        $updateQuery = "UPDATE profiles SET name = ?, age = ?, instagram = ?, youtube = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$name, $age, $instagram, $youtube, $userId]);

        echo "Profil byl úspěšně aktualizován.";
    } else {
        // Profil neexistuje, provedeme vytvoření nového záznamu
        $createProfileQuery = "INSERT INTO profiles (user_id, name, age, instagram, youtube) VALUES (?, ?, ?, ?, ?)";
        $createProfileStmt = $conn->prepare($createProfileQuery);
        $createProfileStmt->execute([$userId, $name, $age, $instagram, $youtube]);

        handleProfileImageUpload($_FILES['profile_image'], $userId, $conn);

        echo "Profil byl úspěšně vytvořen.";
    }

    $conn = null;
}

// Načtení profilových informací
$conn = connectToDatabase($dbConfig['host'], $dbConfig['db_name'], $dbConfig['username'], $dbConfig['password']);

$userId = $_SESSION['user_id'];
$profileQuery = "SELECT * FROM profiles WHERE user_id = ?";
$profileStmt = $conn->prepare($profileQuery);
$profileStmt->execute([$userId]);
$profile = $profileStmt->fetch(PDO::FETCH_ASSOC);

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
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .profile-picture {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <h1 class="mb-4">User Profile</h1>

        <?php
            if (isset($_POST['edit_profile'])) {
                echo '<form method="post" enctype="multipart/form-data">';
            } else {
                echo '<form method="post" enctype="multipart/form-data" style="display: none;">';
            }
        ?>

            <div class="form-group">
                <label for="profile_image">Profile Image:</label>
                <input type="file" class="form-control-file" name="profile_image" accept="image/*">
                <?php
                    // Zobrazit nahraný obrázek, pokud existuje
                    if (isset($profile['profile_image'])) {
                        echo '<div class="profile-picture-container">';
                        echo '<img src="' . $profile['profile_image'] . '" class="profile-picture" alt="Profile Image">';
                        echo '</div>';
                    }
                ?>
            </div>

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" name="name" value="<?php echo isset($profile['name']) ? $profile['name'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" class="form-control" name="age" value="<?php echo isset($profile['age']) ? $profile['age'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="instagram">Instagram:</label>
                <input type="text" class="form-control" name="instagram" value="<?php echo isset($profile['instagram']) ? $profile['instagram'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="youtube">YouTube:</label>
                <input type="text" class="form-control" name="youtube" value="<?php echo isset($profile['youtube']) ? $profile['youtube'] : ''; ?>">
            </div>

            <button type="submit" class="btn btn-primary" name="save_profile">Save Profile</button>
        </form>

        <div id="profile-info">
            <?php
                // Zobrazit profilové informace
                if (isset($profile['profile_image'])) {
                    echo '<div class="profile-picture-container">';
                    echo '<img src="' . $profile['profile_image'] . '" class="profile-picture" alt="Profile Image">';
                    echo '</div>';
                }

                echo '<p><strong>Name:</strong> ' . (isset($profile['name']) ? $profile['name'] : '') . '</p>';
                echo '<p><strong>Age:</strong> ' . (isset($profile['age']) ? $profile['age'] : '') . '</p>';
                echo '<p><strong>Instagram:</strong> ' . (isset($profile['instagram']) ? $profile['instagram'] : '') . '</p>';
                echo '<p><strong>YouTube:</strong> ' . (isset($profile['youtube']) ? $profile['youtube'] : '') . '</p>';
            ?>
            <a href="#" id="edit-profile-btn" class="btn btn-secondary">Edit Profile</a>
            <a href="index.html" class="btn btn-secondary">Back to Index</a>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('edit-profile-btn').addEventListener('click', function() {
            document.getElementById('profile-info').style.display = 'none';
            document.querySelector('form').style.display = 'block';
        });
    </script>
</body>
</html>
