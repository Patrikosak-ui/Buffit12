<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['video_id'])) {
    $videoId = $_POST['video_id'];

    // Database connection
    $host = "md66.wedos.net";
    $db_name = "d230417_buffit";
    $username = "a230417_buffit";
    $password = "495804Patrik.";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get video path to delete file
        $stmt = $conn->prepare("SELECT Video_path FROM Soutezni_videa WHERE ID_videa = ?");
        $stmt->execute([$videoId]);
        $videoPath = $stmt->fetchColumn();

        // Delete video from database
        $removeQuery = "DELETE FROM Soutezni_videa WHERE ID_videa = ?";
        $removeStmt = $conn->prepare($removeQuery);
        $removeStmt->execute([$videoId]);

        // Delete video file from server
        if (file_exists($videoPath)) {
            unlink($videoPath);
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Neplatný požadavek']);
}
?>
