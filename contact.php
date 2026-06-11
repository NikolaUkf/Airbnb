<?php
session_start();
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

class Database 
{
    private const HOST = 'localhost';
    private const NAME = 'villa_agency';
    private const USER = 'root';
    private const PASS = '';

    public static function connect(): PDO 
    {
        return new PDO(
            "mysql:host=" . self::HOST . ";dbname=" . self::NAME . ";charset=utf8mb4",
            self::USER,
            self::PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
}

class ContactMessage 
{
    private PDO $db;

    public function __construct() 
    {
        $this->db = Database::connect();
    }

    public function save(string $name, string $email, string $subject, string $message, string $ip): void 
    {
        $stmt = $this->db->prepare(
            "INSERT INTO contact_messages (name, email, subject, message, ip_address) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$name, $email, $subject, $message, $ip]);
    }
}

class ContactFormHandler 
{
    public function handle(): void 
    {
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => ''];

        try {
            $name    = trim(htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'));
            $email   = trim(htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'));
            $subject = trim(htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8'));
            $message = trim(htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'));
            $ip      = $_SERVER['REMOTE_ADDR'];

            if (empty($name)) {
                throw new Exception('Meno je povinné.');
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Zadaj platnú emailovú adresu.');
            }
            if (empty($message)) {
                throw new Exception('Správa je povinná.');
            }

            $model = new ContactMessage();
            $model->save($name, $email, $subject, $message, $ip);

            $response['success'] = true;
            $response['message'] = 'Správa odoslaná!';

        } catch (Exception $e) {
            $response['message'] = 'Chyba: ' . $e->getMessage();
            error_log('Contact form error: ' . $e->getMessage());
        }

        echo json_encode($response);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new ContactFormHandler();
    $handler->handle();
}
?>
<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title>Villa Agency - Kontakt</title>

    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="navbar/navbar.css">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
</head>

<body>
<?php include 'partials/head.php'; ?>

<div class="page-heading header-text">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <span class="breadcrumb">Kontakt</span>
                <h3>Kontaktujte nás</h3>
            </div>
        </div>
    </div>
</div>

<div class="contact-page section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="section-heading">
                    <h6>Kontaktujte nás</h6>
                    <h2>Skontaktujte našich agentov</h2>
                </div>
                <p>V prípade akýchkoľvek otázok nás neváhajte skontaktovať. Naši PR pracovníci vám s radosťou poradia. Napíšte nám, aké máte pocity pri používaní našej stránky.</p>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="item phone">
                            <img src="assets/images/phone-icon.png" alt="" style="max-width: 52px;">
                            <h6>010-020-0340<br><span>Tel. číslo</span></h6>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="item email">
                            <img src="assets/images/email-icon.png" alt="" style="max-width: 52px;">
                            <h6>info@villa.co<br><span>Email</span></h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <form id="contact-form" action="contact.php" method="post">
                    <div class="row">
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="name">Meno a priezvisko</label>
                                <input type="text" name="name" id="name" placeholder="Vaše meno..." autocomplete="on" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="email">Email</label>
                                <input type="text" name="email" id="email" pattern="[^ @]*@[^ @]*" placeholder="E-mail..." required>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="subject">Predmet</label>
                                <input type="text" name="subject" id="subject" placeholder="Predmet..." autocomplete="on">
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="message">Správa</label>
                                <textarea name="message" id="message" placeholder="Správa"></textarea>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <button type="submit" id="form-submit" class="orange-button">Odoslať</button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-12">
                <div id="map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12469.776493332698!2d-80.14036379941481!3d25.907788681148624!2m3!1f357.26927939317244!2f20.870722720054623!3f0!3m2!1i1024!2i768!4f35!3m3!1m2!1s0x88d9add4b4ac788f%3A0xe77469d09480fcdb!2sSunny%20Isles%20Beach!5e1!3m2!1sen!2sth!4v1642869952544!5m2!1sen!2sth" width="100%" height="500px" frameborder="0" style="border:0; border-radius: 10px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.15);" allowfullscreen=""></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/js/isotope.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/counter.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/contact-form.js"></script>

</body>
</html>