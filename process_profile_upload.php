<?php

$host = "md66.wedos.net";
$db_name = "d230417_buffit";
$username = "a230417_buffit";
$password = "n6T3uSvj";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Chyba připojení k databázi: " . $e->getMessage();
    exit(); 
}


$nickname = $_POST['nickname'];
$age = $_POST['age'];
$instagram = $_POST['instagram'];
$youtube = $_POST['youtube'];


$sql = "INSERT INTO Profil (Nickname, Age, Instagram, Youtube) VALUES (:nickname, :age, :instagram, :youtube)";


try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nickname', $nickname);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':instagram', $instagram);
    $stmt->bindParam(':youtube', $youtube);
    $stmt->execute();

    echo "Informace o uživateli byly úspěšně uloženy do databáze.";
} catch (PDOException $e) {
    echo "Chyba při ukládání informací o uživateli: " . $e->getMessage();
}


$conn = null;
?>
