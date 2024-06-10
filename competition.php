<?php
session_start();

// Kontrola, zda je uživatel přihlášen
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Připojení k databázi pomocí PDO
$host = "md66.wedos.net";
$db_name = "d230417_buffit";
$username = "a230417_buffit";
$password = "495804Patrik.";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Nastavení UTF-8 pro spojení s databází
    $conn->exec("SET NAMES 'utf8mb4'");
    $conn->exec("SET CHARACTER SET utf8mb4");
    $conn->exec("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");
} catch (PDOException $e) {
    echo "Chyba připojení k databázi: " . $e->getMessage();
    exit();
}

// Získání ID soutěže z URL parametru
$event_id = isset($_GET['ID_souteze']) ? $_GET['ID_souteze'] : null;

// Získání detailu soutěže z tabulky Souteze
$eventQuery = "SELECT ID_souteze, user_id, event_name, event_start_date, event_end_date, odkaz, popis FROM Souteze WHERE ID_souteze = :event_id";
$eventStmt = $conn->prepare($eventQuery);
$eventStmt->bindParam(':event_id', $event_id);
$eventStmt->execute();
$event = $eventStmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail soutěže</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .event {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .event h3 {
            margin-top: 0;
            margin-bottom: 10px; /* Přidáno pro oddělení popisu od nadpisu */
        }
        .event p {
            margin-bottom: 5px; /* Přidáno pro oddělení jednotlivých odstavců */
            display: block; /* Popis se bude zobrazovat na novém řádku */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Detail soutěže</h1>
        <a href="index.php" class="btn btn-primary mb-3">Zpět na hlavní stránku</a>
        <?php if ($event): ?>
            <div class="event">
                <h3><?php echo htmlspecialchars_decode($event['event_name']); ?></h3>
                <p><strong>Začátek:</strong> <?php echo htmlspecialchars_decode(date("d.m.Y", strtotime($event['event_start_date']))); ?></p>
                <p><strong>Konec:</strong> <?php echo htmlspecialchars_decode(date("d.m.Y", strtotime($event['event_end_date']))); ?></p>
                <p><strong>Odkaz:</strong> <a href="<?php echo htmlspecialchars_decode($event['odkaz']); ?>" target="_blank"><?php echo htmlspecialchars_decode($event['odkaz']); ?></a></p>
                <p><strong>Popis:</strong> <?php echo htmlspecialchars_decode(htmlspecialchars_decode($event['popis'])); ?></p>
                <a href="upload_videa.php?event_id=<?php echo $event['ID_souteze']; ?>&event_title=<?php echo urlencode(htmlspecialchars_decode($event['event_name'])); ?>" class="btn btn-success">Přihlásit se do soutěže</a>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                Soutěž nebyla nalezena.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
