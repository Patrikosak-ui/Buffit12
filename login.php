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

        <?php
      
       if ($_SERVER["REQUEST_METHOD"] == "POST") {
           // Připojení k databázi
           $host = "MariaDB";
           $db_name = "d230417_rofl";
           $username = "a230417_buffit";
           $password = "n6T3uSvj";
       
           try {
               $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
               $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
           } catch (PDOException $e) {
               echo "Chyba připojení k databázi: " . $e->getMessage();
           }
       
           $email = $_POST["email"];
           $heslo = $_POST["heslo"];
       
           // Příprava a provedení SQL dotazu pro ověření přihlašovacích údajů
           $sql = "SELECT * FROM users WHERE Email = :email";
           $stmt = $conn->prepare($sql);
       
           $stmt->bindParam(':email', $email);
       
           try {
               $stmt->execute();
               $user = $stmt->fetch(PDO::FETCH_ASSOC);
       
               if ($user && password_verify($heslo, $user['Heslo'])) {
                   // Přihlášení úspěšné, přesměrujte na index.html
                   header("Location: index.html");
                   exit();
               } else {
                   echo "Neplatné přihlašovací údaje. Zkuste to znovu nebo <a href='register.php'>se zaregistrujte</a>.";
               }
           } catch (PDOException $e) {
               echo "Chyba při přihlašování: " . $e->getMessage();
           }
       }
    
       
        ?>

    </main>

</body>
</html>
