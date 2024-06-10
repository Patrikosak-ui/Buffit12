<?php
// Připojení k databázi
function connectToDatabase() {
    $host = "md66.wedos.net";
    $db_name = "d230417_buffit";
    $username = "a230417_buffit";
    $password = "495804Patrik.";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'msg' => 'Chyba připojení k databázi: ' . $e->getMessage()]);
        exit();
    }
}

$conn = connectToDatabase();

// Načtení událostí z databáze
try {
    $query = $conn->prepare("SELECT ID_souteze, event_name, event_start_date, event_end_date, odkaz, popis FROM Souteze");
    $query->execute();
    $events = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => true, 'data' => $events]);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'msg' => 'Chyba při načítání událostí: ' . $e->getMessage()]);
}
?>
