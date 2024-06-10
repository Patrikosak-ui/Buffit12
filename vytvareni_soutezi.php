<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vytvoření soutěže</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        #calendar-container {
            width: 100%;
            max-width: 900px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .modal-header {
            background-color: #007bff;
            color: #ffffff;
            border-radius: 8px 8px 0 0;
            border-bottom: none;
        }

        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .modal-body {
            background-color: #f0f2f5;
            border-radius: 0 0 8px 8px;
        }

        .modal-footer {
            border-top: none;
            border-radius: 0 0 8px 8px;
            background-color: #ffffff;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/tk0u9mkheofsmjekgd1sw7q42pa57d5w0z5vqaym2zz9k8mk/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#popis',
            plugins: 'link image code',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link image',
            language: 'cs'
        });
    </script>
</head>
<body>

<div id="calendar-container">
    <div id="calendar"></div>
</div>

<!-- Formulář pro přidání/úpravu události -->
<div class="modal fade" id="event_entry_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Přidat/úpravit událost</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="event_id">
                <div class="form-group">
                    <label for="event_name">Název události</label>
                    <input type="text" name="event_name" id="event_name" class="form-control" placeholder="Zadejte název události">
                </div>
                <div class="form-group">
                    <label for="event_start_date">Počáteční datum</label>
                    <input type="date" name="event_start_date" id="event_start_date" class="form-control" placeholder="Počáteční datum události">
                </div>
                <div class="form-group">
                    <label for="event_end_date">Konečné datum</label>
                    <input type="date" name="event_end_date" id="event_end_date" class="form-control" placeholder="Konečné datum události">
                </div>
                <div class="form-group">
                    <label for="odkaz">Odkaz na Raw materiál</label>
                    <input type="text" name="odkaz" id="odkaz" class="form-control" placeholder="Odkaz na další informace">
                </div>
                <div class="form-group">
                    <label for="popis">Popis</label>
                    <textarea name="popis" id="popis" class="form-control" placeholder="Popis události"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="save_event()">Uložit událost</button>
                <button type="button" class="btn btn-danger" onclick="delete_event()">Smazat událost</button>
            </div>
        </div>
    </div>
</div>

<!-- JS for jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<!-- JS for moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<!-- JS for fullcalendar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
<!-- JS for bootstrap -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<!-- FullCalendar translation for Czech -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/locale/cs.js"></script>

<script>
$(document).ready(function() {
    display_events();
});

function display_events() {
    $.ajax({
        url: 'display_event.php',
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                var events = response.data || [];
                $('#calendar').fullCalendar({
                    locale: 'cs',
                    defaultView: 'month',
                    editable: true,
                    selectable: true,
                    selectHelper: true,
                    events: events.map(function(event) {
                        return {
                            id: event.event_id,
                            title: event.event_name,
                            start: event.event_start_date,
                            end: moment(event.event_end_date).add(1, 'days').format('YYYY-MM-DD'),
                            description: event.popis
                        };
                    }),
                    select: function(start, end) {
                        $('#event_id').val('');
                        $('#event_name').val('');
                        $('#event_start_date').val(moment(start).format('YYYY-MM-DD'));
                        $('#event_end_date').val(moment(end).subtract(1, 'days').format('YYYY-MM-DD'));
                        $('#odkaz').val('');
                        tinymce.get('popis').setContent('');
                        $('#event_entry_modal').modal('show');
                    },
                    eventClick: function(calEvent, jsEvent, view) {
                        jsEvent.preventDefault(); // Zabránit výchozí akci
                        $('#event_id').val(calEvent.id);
                        $('#event_name').val(calEvent.title);
                        $('#event_start_date').val(moment(calEvent.start).format('YYYY-MM-DD'));
                        $('#event_end_date').val(moment(calEvent.end).subtract(1, 'days').format('YYYY-MM-DD'));
                        tinymce.get('popis').setContent(calEvent.description);
                        $('#event_entry_modal').modal('show');
                    }
                });
            } else {
                alert("Chyba při načítání událostí: " + response.msg);
            }
        },
        error: function(xhr, status) {
            alert("Chyba při načítání událostí");
        }
    });
}

function save_event() {
    var event_id = $("#event_id").val();
    var event_name = $("#event_name").val();
    var event_start_date = $("#event_start_date").val();
    var event_end_date = $("#event_end_date").val();
    var odkaz = $("#odkaz").val();
    var popis = tinymce.get('popis').getContent();

    var start_date = moment(event_start_date).format('YYYY-MM-DD');
    var end_date = moment(event_end_date).format('YYYY-MM-DD');
    
    var today = moment().format('YYYY-MM-DD');
    if (moment(start_date).isBefore(today) || moment(end_date).isBefore(today)) {
        alert("Nelze vytvořit soutěž s datem v minulosti.");
        return false;
    }

    if (event_name === "" || event_start_date === "" || event_end_date === "") {
        alert("Prosím, vyplňte všechny požadované údaje.");
        return false;
    }
    $.ajax({
        url: "save_event.php",
        type: "POST",
        dataType: 'json',
        data: {
            event_id: event_id,
            event_name: event_name,
            event_start_date: start_date,
            event_end_date: end_date,
            odkaz: odkaz,
            popis: popis
        },
        success: function(response) {
            $('#event_entry_modal').modal('hide');
            if (response.status) {
                alert(response.msg);
                location.reload();
            } else {
                alert(response.msg);
            }
        },
        error: function(xhr, status) {
            alert("Chyba při ukládání události");
        }
    });
    return false;
}

function delete_event() {
    var event_id = $("#event_id").val();

    if (event_id) {
        $.ajax({
            url: "delete_event.php",
            type: "POST",
            dataType: 'json',
            data: { event_id: event_id },
            success: function(response) {
                $('#event_entry_modal').modal('hide');
                if (response.status) {
                    alert(response.msg);
                    location.reload();
                } else {
                    alert(response.msg);
                }
            },
            error: function(xhr, status) {
                alert("Chyba při mazání události");
            }
        });
    } else {
        alert("Prosím, vyberte událost k odstranění.");
    }
}
</script>

</body>
</html>
