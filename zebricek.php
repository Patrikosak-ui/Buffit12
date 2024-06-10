<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Žebříček nejlepších tvůrců</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .ranking-container {
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .ranking-col {
            flex: 0 0 45%;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Žebříček nejlepších tvůrců</h2>
        <div class="row">
            <?php
            // Připojení k databázi pomocí PDO
            $host = "md66.wedos.net";
            $db_name = "d230417_buffit";
            $username = "a230417_buffit";
            $password = "495804Patrik.";

            try {
                $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Chyba připojení k databázi: " . $e->getMessage();
                exit();
            }

            // Dotaz na získání nejlepších tvůrců s minimálně 10 hodnotiteli
            $rankingQuery = "SELECT u.ID_user, u.jmeno, COUNT(*) AS number_of_ratings, AVG(h.Pocet_hvezd) AS average_rating FROM Hodnoceni h JOIN Users u ON h.ID_user = u.ID_user GROUP BY u.ID_user HAVING number_of_ratings >= 10 ORDER BY AVG(h.Pocet_hvezd) DESC, COUNT(*) DESC";
            $rankingStmt = $conn->prepare($rankingQuery);
            $rankingStmt->execute();
            $ranking = $rankingStmt->fetchAll(PDO::FETCH_ASSOC);

            // Výpis žebříčku
            foreach ($ranking as $position => $creator) {
                // Zde můžete provést další dotazy na získání informací o tvůrcích pomocí jejich ID
                echo '<div class="ranking-col">';
                echo '<div class="ranking-container">';
                echo '<h3>' . ($position + 1) . '. <a href="profil.php?id=' . $creator['ID_user'] . '">' . htmlspecialchars($creator['jmeno']) . '</a></h3>';
                echo '<p>Počet hodnotitelů: ' . htmlspecialchars($creator['number_of_ratings']) . '</p>';
                echo '<p>Průměrné hodnocení: ' . number_format($creator['average_rating'], 2) . '</p>';
                // Zde můžete přidat další informace o tvůrcích podle potřeby
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
