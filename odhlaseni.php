<?php
session_start();

if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
    $message = "Uživatel byl úspěšně odhlášen.";
} else {
    $message = "Uživatel nebyl přihlášen.";
}

header("Location: index.php?message=" . urlencode($message));
exit();
?>
