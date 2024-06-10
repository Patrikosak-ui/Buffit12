<?php
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $video_id = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

    if ($video_id > 0 && $rating > 0 && $user_id > 0) {
        // Database connection using PDO
        $host = "md66.wedos.net";
        $db_name = "d230417_buffit";
        $username = "a230417_buffit";
        $password = "495804Patrik.";

        try {
            $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if user has already rated this video
            $query = "SELECT COUNT(*) FROM Hodnoceni WHERE ID_user = :user_id AND ID_videa = :video_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':video_id', $video_id, PDO::PARAM_INT);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                // Update existing rating
                $query = "UPDATE Hodnoceni SET Pocet_hvezd = :rating WHERE ID_user = :user_id AND ID_videa = :video_id";
                $stmt = $conn->prepare($query);
            } else {
                // Insert new rating
                $query = "INSERT INTO Hodnoceni (ID_user, ID_videa, Pocet_hvezd) VALUES (:user_id, :video_id, :rating)";
                $stmt = $conn->prepare($query);
            }

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':video_id', $video_id, PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
