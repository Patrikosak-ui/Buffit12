<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Pokud uživatel není přihlášen, přesměrovat na přihlašovací stránku
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Buffit</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="buffit.png" rel="icon">
  <link href="buffit.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="style.css" rel="stylesheet">

  <style>
    /* Additional custom styles can go here */
  </style>
</head>

<body>

  <!-- ======= Top Bar ======= -->
  <section id="topbar" class="d-flex align-items-center">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info d-flex align-items-center">
        <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:info@buffit.cz">info@buffit.cz
          </a></i>
      </div>
      <div class="social-links d-none d-md-flex align-items-center">
        <a href="https://www.facebook.com/buffityt" class="facebook"><i class="bi bi-facebook"></i></a>
        <a href="https://www.instagram.com/buffityt/?hl=cs" class="instagram"><i class="bi bi-instagram"></i></a>
      </div>
    </div>
  </section>

  <!-- ======= Header ======= -->
  <style>
    .overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.7);
        z-index: 2;
        cursor: pointer;
    }

    .overlay-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60%;
        height: 80%;
        background-color: white;
        padding: 20px;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        cursor: pointer;
    }
</style>

<header id="header" class="d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo"><img src="buffit.png" alt=""></a>
        <nav id="navbar" class="navbar">
            <ul>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a class="nav-link scrollto active" href="register.php">Registrace</a></li>
                    <li><a class="nav-link scrollto" href="login.php">Přihlášení</a></li>
                <?php endif; ?>
                <li><a class="nav-link scrollto" href="statistiky.php">Žebříček nejlepších tvůrců</a></li>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 8): ?>
                    <li><a class="nav-link scrollto" href="vytvareni_soutezi.php">Vytvořit soutěž</a></li>
                <?php endif; ?>
                <li><a class="nav-link scrollto" href="#" onclick="openProfile()">Profil</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a class="nav-link scrollto" href="odhlaseni.php">Odhlásit se</a></li>
                <?php endif; ?>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav><!-- .navbar -->
    </div>
</header>


<div id="profile-overlay" class="overlay">
    <div class="overlay-content">
        <span class="close-btn" onclick="closeProfile()">&times;</span>
        <iframe name="mainframe" src="profil.php" frameborder="0" width="100%" height="100%"></iframe>
    </div>
</div>

<script>
    function openProfile() {
        document.getElementById("profile-overlay").style.display = "block";
    }

    function closeProfile() {
        document.getElementById("profile-overlay").style.display = "none";
    }
</script>

   


  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center" style="background-image: url('assets/img/hero-bg.jpg');">
    <div class="container" data-aos="zoom-out" data-aos-delay="100">
      
      <h1>Vítej na stránce <span>Buffit</span></h1>
      <h2>Chceš vytvářet videa a zlepšovat se, nebo chceš jenom nahlídnout tak Klikej!!</h2>
      <div class="d-flex">
        <p><a href="#about" class="btn-get-started scrollto">Přidat se do soutěže</a></p>
        <p><a href="#services" class="btn-get-started scrollto">Aktivní Soutěže</a></p>
      </div>
    </div>
</section>

 

  

    <!-- ======= About Section ======= -->
    <section id="about" class="about section">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2>Přidat se do soutěže</h2>
            <h3><span>Přehled soutěží</span></h3>
        </div>
        <?php
session_start();

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
            color: #ffffff; /* Bílá barva textu */
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
        /* Bílá barva textu u názvu události */
        .fc-title {
            color: #ffffff !important; /* Bílá barva textu */
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
                        <a href="competition.php" id="registerButton" class="btn btn-success">Detail Soutěže</a>
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
                locale: 'cs', // Nastavení češtiny
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: false, // Události nejsou editovatelné
                events: <?php echo json_encode($events, JSON_UNESCAPED_UNICODE); ?>,
                eventClick: function(event) {
                    $('#eventName').text(event.title);
                    $('#eventStartDate').text(moment(event.start).format('LL')); // Formát datumu na český
                    $('#eventEndDate').text(moment(event.end).format('LL')); // Formát datumu na český
                    $('#eventOdkaz').html('<a href="'+ event.odkaz +'" target="_blank">'+ event.odkaz +'</a>');
                    $('#eventPopis').text(event.popis);
                    $('#detailButton').attr('href', 'competition.php?ID_souteze=' + event.event_id);
                    $('#eventModal').modal('show');
                    return false; // Z
                }
            });

            $('#eventModal').on('hidden.bs.modal', function () {
                $('#eventName').empty();
                $('#eventStartDate').empty();
                $('#eventEndDate').empty();
                $('#eventOdkaz').empty();
                $('#eventPopis').empty();
                $('#detailButton').attr('href', '#');
            });
        });
    </script>
</body>
</html>

 
    </section><!-- End About Section -->
    
</a>

 
   
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        /* Importing fonts from Google */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');

        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: white;
            padding: 0px;
        }

        .section {
            margin-bottom: 50px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-wrapper {
            max-width: 450px;
            margin: 0 auto;
            background-color: #494c4e;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 10px 10px 20px #1e1f21, -10px -10px 20px #75787a;
        }

        .logoo {
            width: 100px;
            margin: 0 auto 20px auto;
            text-align: center;
        }

        .logoo img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 50%;
            box-shadow: 0px 0px 5px #5f5f5f, 0px 0px 0px 8px #494c4e, 10px 10px 20px #a7aaa7, -10px -10px 20px #fff;
        }

        .form-field {
            margin-bottom: 20px;
        }

        .form-field label {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 8px;
            display: block;
        }

        .form-field input,
        .form-field textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            color: #fff;
            background-color: #3c3f41;
        }

        .form-field textarea {
            height: 100px;
            resize: none;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 5px;
            background-color: #03A9F4;
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #039BE5;
        }
    </style>
</head>
<body>






  </section>
</a>


       
 


  <!-- ======= Services Section ======= -->
  <section id="services" class="services">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2>Aktivní soutěže</h2>
          <h3><span>Soutěže</span></h3>
        </div>
        <?php
session_start();

$host = "md66.wedos.net";
$db_name = "d230417_buffit";
$username = "a230417_buffit";
$password = "495804Patrik.";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Získání soutěží z tabulky Souteze
    $soutezQuery = "SELECT ID_souteze, event_name, event_start_date, event_end_date, popis FROM Souteze";
    $soutezStmt = $conn->prepare($soutezQuery);
    $soutezStmt->execute();
    $souteze = $soutezStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Chyba připojení k databázi: " . $e->getMessage();
    exit();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam Soutěží</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card:hover {
            transform: scale(1.05);
            transition: transform 0.2s;
        }
        .card {
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        
        <div class="row">
            <?php foreach ($souteze as $soutez): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo html_entity_decode($soutez['event_name']); ?></h5>
                            <p class="card-text"><strong>Začátek:</strong> <?php echo html_entity_decode($soutez['event_start_date']); ?></p>
                            <p class="card-text"><strong>Konec:</strong> <?php echo html_entity_decode($soutez['event_end_date']); ?></p>
                            <p class="card-text"><?php echo html_entity_decode($soutez['popis']); ?></p>
                            <a href="videa_souteze.php?ID_souteze=<?php echo $soutez['ID_souteze']; ?>" class="btn btn-primary">Zobrazit videa</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>



       













</section>
   


  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  

    <div class="footer-top">
      <div class="container">
        <div class="row">

          <div class="col-lg-3 col-md-6 footer-contact">
            <h3>BizLand<span>.</span></h3>
            <p>
               Výstupní 3219, 400 11 Ústí nad Labem-Severní Terasa <br>
               Ústí nad Labem<br>
               Česká Republika <br><br>
              <strong>Phone:</strong> +420 734 712 312<br>
              <strong>Email:</strong> info@buffit.cz<br>
            </p>
          </div>

          

          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Socialní Sítě</h4>
           
            <div class="social-links mt-3">
              
              <a href="https://www.facebook.com/search/top/?q=buff%20it" class="facebook"><i class="bx bxl-facebook"></i></a>
              <a href="https://www.instagram.com/buffityt/?hl=cs" class="instagram"><i class="bx bxl-instagram"></i></a>
              
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="container py-4">
      <div class="copyright">
        &copy; Copyright <strong><span>BizLand</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/bizland-bootstrap-business-template/ -->
        Design by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>
  </footer><!-- End Footer -->

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>