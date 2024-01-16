<?php
session_start();

function connectToDatabase() {
    $host = "md66.wedos.net";
    $db_name = "d230417_buffit";
    $username = "a230417_buffit";
    $password = "n6T3uSvj";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo "Chyba připojení k databázi: " . $e->getMessage();
        exit();
    }
}

function disconnectFromDatabase($conn) {
    $conn = null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = connectToDatabase();

    $email = $_POST["email"];
    $heslo = $_POST["heslo"];

    $sql = "SELECT * FROM Users WHERE Email = :email";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':email', $email);

    try {
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($heslo, $user['Heslo'])) {
            $_SESSION['user_id'] = $user['ID_user'];
            header("Location: index.html"); // Přesměrování na dashboard.php nebo jinou stránku
            exit();
        } else {
            echo "Neplatné přihlašovací údaje. Zkuste to znovu nebo <a href='register.php'>se zaregistrujte</a>.";
        }
    } catch (PDOException $e) {
        echo "Chyba při přihlašování: " . $e->getMessage();
    } finally {
        disconnectFromDatabase($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Přihlášení</title>
</head>
<body>

    <main>
        <h2>Přihlášení</h2>
        <form action="login.php" method="post">
            Email: <input type="email" name="email" required><br>
            Heslo: <input type="password" name="heslo" required><br>
            <input type="submit" value="Přihlásit">
        </form>
        
        <p>Tady se můžeš <a href="register.php">registrovat</a>.</p>
    </main>

</body>
</html>
