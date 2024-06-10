<?php
session_start();

function connectToDatabase() {
    $host = "md66.wedos.net";
    $db_name = "d230417_buffit";
    $username = "a230417_buffit";
    $password = "495804Patrik.";

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
            header("Location: index.php"); // Přesměrování na dashboard.php nebo jinou stránku
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
    <link rel="stylesheet" href="logiin.css">
    <title>Logina</title>
</head>
<body>

<div class="wrapper">
    <div class="logo">
        <img src="logo_white2.png" alt="">
    </div>
    <div class="text-center mt-4 name">
        Buffit
    </div>
    <form class="p-3 mt-3" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-field d-flex align-items-center">
            <span class="far fa-user"></span>
            <input type="text" name="email" id="email" placeholder="Email">
        </div>
        <div class="form-field d-flex align-items-center">
            <span class="fas fa-key"></span>
            <input type="password" name="heslo" id="heslo" placeholder="Password">
        </div>
        <button type="submit" class="btn mt-3">Přihlášení</button>
    </form>
    <div class="text-center fs-6">
         <a href="register.php">Zaregistrovat se</a>
    </div>
</div>

</body>
</html>