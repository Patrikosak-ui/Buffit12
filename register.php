<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = $_POST['jmeno'];
    $prijmeni = $_POST['prijmeni'];
    $email = $_POST['email'];
    $heslo = password_hash($_POST['heslo'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (jmeno, prijmeni, email, heslo) VALUES ('$jmeno', '$prijmeni', '$email', '$heslo')";

    if ($conn->query($sql) === TRUE) {
        echo "Registrace úspěšná";
    } else {
        echo "Chyba při registraci: " . $conn->error;
    }
}

$conn->close();
?>