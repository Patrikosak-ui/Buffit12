<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Registrace</title>
</head>
<body>

<main>
    <h2>Registrace</h2>

    <form action="register.php" method="post">
        Jméno: <input type="text" name="jmeno" required><br>
        Příjmení: <input type="text" name="prijmeni" required><br>
        Email: <input type="email" name="email" required><br>
        Heslo: <input type="password" name="heslo" required><br>
        Instagram: <input type="text" name="instagram"><br>
        YouTube: <input type="text" name="youtube"><br>
        <input type="submit" value="Registrovat">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Připojení k databázi
        $host = "localhost";
        $db_name = "buffit";
        $username = "root";
        $password = "";

        try {
            $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Chyba připojení k databázi: " . $e->getMessage();
        }

        // Získání hodnot z formuláře
        $jmeno = $_POST["jmeno"];
        $prijmeni = $_POST["prijmeni"];
        $email = $_POST["email"];
        $heslo = password_hash($_POST["heslo"], PASSWORD_DEFAULT); // Hashování hesla
        $instagram = $_POST["instagram"];
        $youtube = $_POST["youtube"];

        // Příprava a provedení SQL dotazu pro vložení do tabulky users
        $sql = "INSERT INTO users (jmeno, prijmeni, email, heslo, Instagram, Youtube) VALUES (:jmeno, :prijmeni, :email, :heslo, :instagram, :youtube)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':jmeno', $jmeno);
        $stmt->bindParam(':prijmeni', $prijmeni);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':heslo', $heslo);
        $stmt->bindParam(':instagram', $instagram);
        $stmt->bindParam(':youtube', $youtube);

        try {
            $stmt->execute();
            echo "Registrace úspěšná. Nyní se můžete <a href='login.php'>přihlásit</a>.";
        } catch (PDOException $e) {
            echo "Chyba při registraci: " . $e->getMessage();
        }
    }
    ?>
</main>

</body>
</html>
