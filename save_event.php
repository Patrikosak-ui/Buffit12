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
    $ID_souteze = $_POST['ID_souteze'];
    $event_name = $_POST['event_name'];
    $event_start_date = $_POST['event_start_date'];
    $event_end_date = $_POST['event_end_date'];
    $odkaz = $_POST['odkaz'];
    $popis = $_POST['popis'];

    if (empty($event_name) || empty($event_start_date) || empty($event_end_date)) {
        echo json_encode(['status' => false, 'msg' => 'Všechny údaje jsou povinné.']);
        exit();
    }

    $conn = connectToDatabase();
    $query = $conn->prepare("INSERT INTO Souteze(ID_souteze, event_name, event_start_date, event_end_date, odkaz, popis) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $query->execute([$ID_souteze, $event_name, $event_start_date, $event_end_date, $odkaz, $popis]);

    if ($result) {
        echo json_encode(['status' => true, 'msg' => 'Událost byla úspěšně uložena.']);
    } else {
        echo json_encode(['status' => false, 'msg' => 'Chyba při ukládání události.']);
    }
}
?>
