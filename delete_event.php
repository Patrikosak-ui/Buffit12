<?php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['ID_souteze'];

    if (empty($event_id)) {
        echo json_encode(['status' => false, 'msg' => 'Nebyla vybrána žádná událost k odstranění.']);
        exit();
    }

    $conn = connectToDatabase();
    $query = $conn->prepare("DELETE FROM Souteze WHERE ID_souteze = ?");
    $result = $query->execute([$event_id]);

    if ($result) {
        echo json_encode(['status' => true, 'msg' => 'Událost byla úspěšně odstraněna.']);
    } else {
        echo json_encode(['status' => false, 'msg' => 'Chyba při odstraňování události.']);
    }
}
?>
