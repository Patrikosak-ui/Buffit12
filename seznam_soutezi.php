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

// Získání událostí z tabulky Souteze
$eventsQuery = "SELECT ID_souteze AS event_id, event_name AS title, event_start_date AS start, event_end_date AS end, odkaz, popis FROM Souteze";
$eventsStmt = $conn->prepare($eventsQuery);
$eventsStmt->execute();
$events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);

// Převést HTML popis na čistý text pro každou událost
foreach ($events as &$event) {
    $event['popis'] = html_entity_decode($event['popis']); // Dekódování HTML entit
    $event['popis'] = strip_tags($event['popis']); // Odstranění veškerých HTML značek
    $event['popis'] = htmlspecialchars($event['popis']); // Konverze speciálních znaků na HTML entity
}
unset($event); // Zrušení reference na poslední položku, abychom zabránili nechtěnému chování v dalším kódu
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam soutěží</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.3/jquery.qtip.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        /* Upravený styl pro události v kalendáři */
        .fc-event {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 14px;
            margin-bottom: 5px;
            transition: background-color 0.3s, transform 0.3s;
        }
        .fc-event:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        /* Upravený styl pro detail události v modálním okně */
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 10px 10px 0 0;
        }
        .modal-title {
            font-weight: bold;
        }
        .modal-body {
            padding: 20px;
        }
        #eventDetails p {
            margin-bottom: 10px;
        }
        #registerButton {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div id="calendar"></div>
        <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventModalLabel">Detail události</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="eventDetails">
                            <p><strong>Název události:</strong> <span id="eventName"></span></p>
                            <p><strong>Začátek:</strong> <span id="eventStartDate"></span></p>
                            <p><strong>Konec:</strong> <span id="eventEndDate"></span></p>
                            <p><strong>Odkaz:</strong> <span id="eventOdkaz"></span></p>
                            <p><strong>Popis:</strong> <span id="eventPopis"></span></p>
                        </div>
                        <a href="upload_videa.php" id="registerButton" class="btn btn-success">Přihlásit se do soutěže</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.3/jquery.qtip.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            moment.locale('cs');
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: false, // Události nejsou editovatelné
                events: <?php echo json_encode($events, JSON_UNESCAPED_UNICODE); ?>,
                eventRender: function(event, element) {
                    element.addClass('event-card');
                    element.qtip({
                        content: {
                            title: event.title,
                            text: '<strong>Začátek:</strong> ' + moment(event.start).format('LL') + '<br>' +
                                  '<strong>Konec:</strong> ' + moment(event.end).format('LL') + '<br>' +
                                  '<strong>Popis:</strong> ' + event.popis
                        },
                        style: {
                            classes: 'qtip-bootstrap'
                        },
                        position: {
                            my: 'top left',
                            at: 'bottom center'
                        }
                    });
                },
                eventClick: function(event) {
                    $('#eventName').text(event.title);
                    $('#eventStartDate').text(moment(event.start).format('LL')); // Formát datumu na český
                    $('#eventEndDate').text(moment(event.end).format('LL')); // Formát datumu na český
                    $('#eventOdkaz').text(event.odkaz);
                    $('#eventPopis').text(event.popis);
                    $('#registerButton').attr('href', 'upload_videa.php?event_id=' + event.event_id + '&event_title=' + encodeURIComponent(event.title));
                    $('#eventModal').modal('show');
                    return false; // Zabránění výchozí akce (otevření URL události v novém okně)
                }
            });

            $('#eventModal').on('hidden.bs.modal', function () {
                $('#eventName').empty();
                $('#eventStartDate').empty();
                $('#eventEndDate').empty();
                $('#eventOdkaz').empty();
                $('#eventPopis').empty();
                $('#registerButton').attr('href', 'upload_videa.php');
            });
        });
    </script>
</body>
</html>
