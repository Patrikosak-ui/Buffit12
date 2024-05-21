<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>register</title>
 
</head>
<body>
    <main>
        <div class="wrapper">
            <div class="logo">
                <img src="logo_white2.png" alt="">
            </div>
            <div class="text-center mt-4 name">
                Buffit
            </div>
            
            <form class="p-3 mt-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-field d-flex align-items-center">
                    <span class="far fa-user"></span>
                    <input type="text" name="jmeno" id="jmeno" placeholder="Jméno" required>
                </div>
                <div class="form-field d-flex align-items-center">
                    <span class="far fa-user"></span>
                    <input type="text" name="prijmeni" id="prijmeni" placeholder="Příjmení" required>
                </div>
                <div class="form-field d-flex align-items-center">
                    <span class="far fa-envelope"></span>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                </div>
                <div class="form-field d-flex align-items-center">
                    <span class="fas fa-key"></span>
                    <input type="password" name="heslo" id="heslo" placeholder="Heslo" required>
                </div>
                <div class="form-field d-flex align-items-center">
                    <span class="fab fa-instagram"></span>
                    <input type="text" name="instagram" id="instagram" placeholder="Instagram">
                </div>
                <div class="form-field d-flex align-items-center">
                    <span class="fab fa-youtube"></span>
                    <input type="text" name="youtube" id="youtube" placeholder="YouTube">
                </div>
                <button type="submit" class="btn mt-3">Registrovat</button>
            </form>
            <div class="text-center fs-6">
                <a href="login.php">Už máte účet? Přihlaste se zde</a>
            </div>
        </div>
    </main>
</body>
</html>


        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $host = "md66.wedos.net";
            $db_name = "d230417_buffit";
            $username = "a230417_buffit";
            $password = "495804Patrik.";
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