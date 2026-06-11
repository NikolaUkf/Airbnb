<?php
session_start();
?>
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
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getBlockedDates(int $propertyId): array
    {
        $stmt = $this->conn->prepare("SELECT date_from, date_to FROM reservations WHERE property_id = ? AND status != 'cancelled'");
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

class ReservationHandler
{
    private PDO $conn;
    private string $message = '';
    private string $messageType = '';

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function handle(int $propertyId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reserve'])) {
            return;
        }

        try {
            $name  = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $from  = trim($_POST['date_from'] ?? '');
            $to    = trim($_POST['date_to'] ?? '');

            if (empty($name) || empty($email) || empty($from) || empty($to)) {
                throw new Exception('Vyplňte všetky povinné polia.');
            }

            if ($to <= $from) {
                throw new Exception('Dátum odchodu musí byť po dátume príchodu.');
            }

            $stmt = $this->conn->prepare("INSERT INTO reservations 
                (property_id, name, email, phone, date_from, date_to, message) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $propertyId, 
                $name, 
                $email, 
                trim($_POST['phone'] ?? ''), 
                $from, 
                $to, 
                trim($_POST['message'] ?? '')
            ]);

            $this->message = 'Rezervácia bola úspešne odoslaná!';
            $this->messageType = 'success';

        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function getMessage(): string { return $this->message; }
    public function getMessageType(): string { return $this->messageType; }
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: properties.php');
    exit;
}

$propertyRepo = new PropertyDetailRepository($conn);
$property = $propertyRepo->findById($id);

if (!$property) {
    header('Location: properties.php');
    exit;
}

$blockedDates = $propertyRepo->getBlockedDates($id);

$reservationHandler = new ReservationHandler($conn);
$reservationHandler->handle($id);

$message = $reservationHandler->getMessage();
$messageType = $reservationHandler->getMessageType();
$types = ['villa' => 'Villa', 'apartment' => 'Apartmán', 'penthouse' => 'Penthouse'];
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
                            <input type="text" 
                                   name="name" 
                                   class="form-control" 
                                   placeholder="Vaše meno *" 
                                   value="<?php echo isset($_SESSION['user_username']) ? htmlspecialchars($_SESSION['user_username']) : ''; ?>" 
                                   <?php echo isset($_SESSION['user_username']) ? 'readonly' : ''; ?> 
                                   required>
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   placeholder="Váš email *" 
                                   value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>" 
                                   <?php echo isset($_SESSION['user_email']) ? 'readonly' : ''; ?> 
                                   required>
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <input type="tel" name="phone" class="form-control" placeholder="Telefón">
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <label style="font-size:13px;">Dátum príchodu *</label>
                            <input type="text" id="date_from" name="date_from" class="form-control" placeholder="Vyberte dátum príchodu" required>
                        </div>
                        <div class="form-group" style="margin-bottom:15px;">
                            <label style="font-size:13px;">Dátum odchodu *</label>
                            <input type="text" id="date_to" name="date_to" class="form-control" placeholder="Vyberte dátum odchodu" required>
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/sk.js"></script>

<script>
const bookedDates = <?php echo json_encode($blockedDates); ?>;

const disableConfig = bookedDates.map(range => {
    return {
        from: range.date_from,
        to: range.date_to
    };
});

function addCalendarFooter(instance) {
    if (instance.calendarContainer.querySelector('.flatpickr-footer-legend')) return;

    const footer = document.createElement('div');
    footer.className = 'flatpickr-footer-legend';
    
    footer.innerHTML = `
        <span class="action-btn" onclick="window.currentPicker.clear()">Clear</span>
        <div class="legend-status">
            <div class="color-box"></div>
            <span>Obsadený termín</span>
        </div>
        <span class="action-btn" onclick="window.currentPicker.close()">Today</span>
    `;
    instance.calendarContainer.appendChild(footer);
}

const fromPicker = flatpickr("#date_from", {
    locale: "sk",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d.m.Y",
    minDate: "today",
    disable: disableConfig,
    onOpen: function(selectedDates, dateStr, instance) {
        window.currentPicker = instance;
        addCalendarFooter(instance);
    },
    onChange: function(selectedDates, dateStr, instance) {
        toPicker.set("minDate", dateStr);
    }
});

const toPicker = flatpickr("#date_to", {
    locale: "sk",
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "d.m.Y",
    minDate: "today",
    disable: disableConfig,
    onOpen: function(selectedDates, dateStr, instance) {
        window.currentPicker = instance;
        addCalendarFooter(instance);
    }
});
</script>

</body>
</html>