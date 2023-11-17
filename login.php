<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_email = $_POST['login_email'];
    $login_heslo = $_POST['login_heslo'];

    $sql = "SELECT * FROM users WHERE email='$login_email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($login_heslo, $row['heslo'])) {
            echo "Přihlášení úspěšné";
        } else {
            echo "Nesprávné heslo";
        }
    } else {
        echo "Uživatel s tímto e-mailem neexistuje";
    }
}

$conn->close();
?>