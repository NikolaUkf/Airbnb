<?php
include 'create_property/config.php';

class PropertyDetailRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM properties WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}

class ReservationRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function save(int $propertyId, string $name, string $email, string $phone, string $dateFrom, string $dateTo, string $message): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO reservations 
            (property_id, name, email, phone, date_from, date_to, message) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$propertyId, $name, $email, $phone, $dateFrom, $dateTo, $message]);
    }
}

class ReservationValidator
{
    private string $error = '';

    public function validate(array $data): bool
    {
        if (empty($data['name']) || empty($data['email']) || empty($data['date_from']) || empty($data['date_to'])) {
            $this->error = 'Vyplňte všetky povinné polia.';
            return false;
        }
        if ($data['date_to'] <= $data['date_from']) {
            $this->error = 'Dátum odchodu musí byť po dátume príchodu.';
            return false;
        }
        return true;
    }

    public function getError(): string
    {
        return $this->error;
    }
}

class ReservationController
{
    private ReservationValidator $validator;
    private ReservationRepository $repository;
    private string $message = '';
    private string $messageType = '';

    public function __construct(ReservationValidator $validator, ReservationRepository $repository)
    {
        $this->validator  = $validator;
        $this->repository = $repository;
    }

    public function handle(int $propertyId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reserve'])) return;

        $data = [
            'name'      => trim($_POST['name']      ?? ''),
            'email'     => trim($_POST['email']     ?? ''),
            'phone'     => trim($_POST['phone']     ?? ''),
            'date_from' => trim($_POST['date_from'] ?? ''),
            'date_to'   => trim($_POST['date_to']   ?? ''),
            'message'   => trim($_POST['message']   ?? ''),
        ];

        if (!$this->validator->validate($data)) {
            $this->message     = $this->validator->getError();
            $this->messageType = 'error';
            return;
        }

        $this->repository->save($propertyId, $data['name'], $data['email'], $data['phone'], $data['date_from'], $data['date_to'], $data['message']);
        $this->message     = 'Rezervácia bola úspešne odoslaná!';
        $this->messageType = 'success';
    }

    public function getMessage(): string { return $this->message; }
    public function getMessageType(): string { return $this->messageType; }
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: properties.php'); exit; }

$propertyRepo = new PropertyDetailRepository($conn);
$property     = $propertyRepo->findById($id);
if (!$property) { header('Location: properties.php'); exit; }

$controller = new ReservationController(new ReservationValidator(), new ReservationRepository($conn));
$controller->handle($id);

$message     = $controller->getMessage();
$messageType = $controller->getMessageType();
$types       = ['villa' => 'Villa', 'apartment' => 'Apartmán', 'penthouse' => 'Penthouse'];
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title><?php echo htmlspecialchars($property['title']); ?> - Villa Agency</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="assets/css/properties-details.css">
</head>
<body>

<div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
        <span class="dot"></span>
        <div class="dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>

<?php include 'partials/head.php'; ?>

<div class="page-heading header-text">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <span class="breadcrumb"><a href="properties.php">Akomodácie</a> / <?php echo htmlspecialchars($property['title']); ?></span>
                <h3><?php echo htmlspecialchars($property['title']); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="single-property section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="main-image">
                    <img src="create_property/uploads/<?php echo htmlspecialchars($property['image']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
                </div>
                <div class="main-content">
                    <span class="category"><?php echo $types[$property['type']] ?? 'Villa'; ?></span>
                    <h4><?php echo htmlspecialchars($property['address']); ?></h4>
                    <h3 style="color:#f5a425;">€<?php echo number_format($property['price'], 0, ',', '.'); ?> / mesiac</h3>
                </div>
                <div class="info-table" style="margin-top:20px;">
                    <ul>
                        <li>Spálne <span><?php echo $property['bedrooms']; ?></span></li>
                        <li>Kúpeľne <span><?php echo $property['bathrooms']; ?></span></li>
                        <li>Plocha <span><?php echo $property['area']; ?> m²</span></li>
                        <li>Poschodie <span><?php echo $property['floor']; ?></span></li>
                        <li>Parkovanie <span><?php echo $property['parking']; ?> miest</span></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="info-table">
                    <h4 style="margin-bottom:20px;">Rezervovať ubytovanie</h4>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group" style="margin-bottom:15px;">
                            <input type="text" name="name" class="form-control" placeholder="Vaše meno *" required>
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <input type="email" name="email" class="form-control" placeholder="Váš email *" required>
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <input type="tel" name="phone" class="form-control" placeholder="Telefón">
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <label style="font-size:13px;">Dátum príchodu *</label>
                            <input type="date" name="date_from" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <label style="font-size:13px;">Dátum odchodu *</label>
                            <input type="date" name="date_to" class="form-control" required>
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <textarea name="message" class="form-control" placeholder="Správa" rows="3"></textarea>
                        </div>
                        <button type="submit" name="reserve" class="orange-button">Odoslať rezerváciu</button>
                    </form>
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

</body>
</html>