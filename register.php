<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <style>
        body {
            background-color: #494c4e; /* Tmavě šedivá barva pro pozadí */
            font-family: 'Arial', sans-serif;
            color: #fff; /* Bílá barva textu pro kontrast */
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        main {
            background-color: #303336; /* Černá barva pro pozadí formuláře */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            max-height: 600px;
            height: 100%;
        }

        h2 {
            text-align: center;
            color: #494c4e; /* Šedivá barva pro nadpis */
            margin-bottom: 20px;
        }

        form input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #494c4e; /* Tmavě šedivá barva pro pole vstupu */
            color: #fff; /* Bílý text pro kontrast */
        }

        form input[type="text"],
        form input[type="password"],
        form input[type="email"] {
            background-color: #494c4e;
            color: #fff;
        }

        form input[type="submit"] {
            background-color: #303336; /* Černá barva pro tlačítko Registrovat */
            color: #fff;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #494c4e; /* Tmavě šedivý odstín černé při najetí myší */
        }

        /* Přidání responzivních vlastností pro menší obrazovky */
        @media screen and (max-width: 600px) {
            main {
                margin-top: 20px;
            }
        }
    </style>
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
            
            $host = "md66.wedos.net";
            $db_name = "d230417_buffit";
            $username = "a230417_buffit";
            $password = "n6T3uSvj";

            try {
                $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Chyba připojení k databázi: " . $e->getMessage();
            }

          
            $jmeno = $_POST["jmeno"];
            $prijmeni = $_POST["prijmeni"];
            $email = $_POST["email"];
            $heslo = password_hash($_POST["heslo"], PASSWORD_DEFAULT); 
            $instagram = $_POST["instagram"];
            $youtube = $_POST["youtube"];

            $sql = "INSERT INTO Users (Jmeno, Prijmeni, Email, Heslo, Instagram, Youtube) VALUES (:jmeno, :prijmeni, :email, :heslo, :instagram, :youtube)";
            
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
