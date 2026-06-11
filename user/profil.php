<?php
session_start();

if (!isset($_SESSION['user_username'])) {
    header("Location: ../login_system/login.php");
    exit();
}

class Database {
    private static ?mysqli $instance = null;

    public static function get(): mysqli {
        if (!self::$instance) {
            self::$instance = new mysqli("localhost", "root", "", "villa_agency");
            if (self::$instance->connect_error) {
                die("Spojenie zlyhalo: " . self::$instance->connect_error);
            }
            self::$instance->set_charset("utf8mb4");
        }
        return self::$instance;
    }
}

class UserRepository {
    private mysqli $db;

    public function __construct() {
        $this->db = Database::get();
    }

    public function findByEmail(string $email): array {
        $stmt = $this->db->prepare("SELECT full_name, phone FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?? [];
    }

    public function update(string $email, string $fullName, string $phone): bool {
        $stmt = $this->db->prepare("UPDATE users SET full_name = ?, phone = ? WHERE email = ?");
        $stmt->bind_param("sss", $fullName, $phone, $email);
        return $stmt->execute();
    }

    public function getReservations(string $email): array {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE email = ? ORDER BY id DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getMessages(string $email): array {
        $stmt = $this->db->prepare("SELECT * FROM contact_messages WHERE email = ? ORDER BY id DESC");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

class ProfileController {
    private UserRepository $repo;
    public string $username;
    public string $email;
    public array  $userData;
    public array  $reservations;
    public array  $messages;

    public function __construct() {
        $this->repo         = new UserRepository();
        $this->username     = $_SESSION['user_username'];
        $this->email        = $_SESSION['user_email'] ?? '';
        $this->userData     = $this->repo->findByEmail($this->email);
        $this->reservations = $this->repo->getReservations($this->email);
        $this->messages     = $this->repo->getMessages($this->email);
    }

    public function handlePost(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_profile'])) return;

        $fullName = trim($_POST['full_name'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');

        $_SESSION['flash'] = $this->repo->update($this->email, $fullName, $phone)
            ? ['type' => 'success', 'text' => 'Osobné údaje boli úspešne upravené!']
            : ['type' => 'danger',  'text' => 'Chyba pri aktualizácii údajov.'];

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    public static function h(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}

$ctrl = new ProfileController();
$ctrl->handlePost();

$pageTitle  = 'Môj profil';
$pageStyles = ['user/user'];
include '../partials/head.php';
?>

<?php if ($flash = ($_SESSION['flash'] ?? null)): unset($_SESSION['flash']); ?>
<div class="container mt-3">
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
        <i class="fa fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
        <?= ProfileController::h($flash['text']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>

<style>
.profile-container { max-width:1300px; margin:40px auto; padding:0 20px; display:flex; gap:30px; align-items:flex-start; flex-wrap:wrap; }
.profile-card { flex:1; min-width:320px; max-width:400px; }
.avatar { font-size:40px; width:80px; height:80px; border-radius:50%; background:#e0e0e0; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; }
.edit-trigger { cursor:pointer; line-height:0; color:#f35525; transition:color .2s; vertical-align:middle; }
.edit-trigger svg { width:17px; height:17px; fill:currentColor; vertical-align:middle; }
.edit-trigger.active { color:#28a745; }
.profile-label          { font-weight:600; font-size:12px; color:#999; text-transform:uppercase; letter-spacing:.5px; display:block; margin-bottom:6px; }
.profile-label.editable { color:#f35525; }
.profile-input          { width:100%; border:1px solid #e0e0e0; padding:12px 15px; border-radius:10px; font-size:14px; background:#f5f5f5; color:#555; outline:none; transition:.3s; box-sizing:border-box; }
.profile-input.unlocked { background:#fff; color:#1e1e1e; border-color:#f35525; }
.detail-item            { margin-top:15px; }
.btn-save   { background:#f35525; color:#fff; border:none; padding:13px 20px; border-radius:25px; font-weight:600; font-size:14px; cursor:pointer; transition:.3s; width:100%; margin-bottom:12px; display:none; box-shadow:0 4px 10px rgba(243,85,37,.2); }
.btn-logout { display:block; width:100%; text-align:center; border:2px solid #f35525; color:#f35525; background:transparent; padding:11px 20px; border-radius:25px; font-weight:600; font-size:14px; transition:.3s; text-decoration:none; box-sizing:border-box; }
.btn-logout:hover { background:#fef0eb; }
.divider { border:none; border-top:1px solid #f0f0f0; margin:20px 0; }
.profile-content { flex:2; min-width:500px; }
</style>

<div class="profile-container">

    <aside class="profile-card">
        <div style="text-align:center">
            <div class="avatar">👤</div>
            <h2 style="font-size:1.2rem; margin-bottom:4px; display:flex; align-items:center; justify-content:center; gap:8px;">
                Ahoj, <?= ProfileController::h($ctrl->username) ?>!
                <span id="edit-trigger" class="edit-trigger" title="Upraviť profil">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm17.71-10.21a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                    </svg>
                </span>
            </h2>
            <p style="color:#888; font-size:.85rem; margin-bottom:16px;">Člen od: 2026</p>
        </div>

        <hr class="divider">

        <form method="POST" id="profile-form">
            <div class="detail-item">
                <span class="profile-label">Používateľské meno:</span>
                <input class="profile-input" type="text" value="<?= ProfileController::h($ctrl->username) ?>" readonly>
            </div>
            <div class="detail-item">
                <span class="profile-label">E-mailová adresa:</span>
                <input class="profile-input" type="text" value="<?= ProfileController::h($ctrl->email) ?>" readonly>
            </div>

            <div class="detail-item">
                <label class="profile-label editable">Celé meno:</label>
                <input class="profile-input" id="inp-name" type="text" name="full_name"
                       value="<?= ProfileController::h($ctrl->userData['full_name'] ?? '') ?>"
                       placeholder="Napr. Janko Hraško" readonly>
            </div>
            <div class="detail-item" style="margin-bottom:20px;">
                <label class="profile-label editable">Telefónne číslo:</label>
                <input class="profile-input" id="inp-phone" type="tel" name="phone"
                       value="<?= ProfileController::h($ctrl->userData['phone'] ?? '') ?>"
                       placeholder="Napr. +421..." readonly>
            </div>

            <button type="submit" name="update_profile" id="btn-save" class="btn-save">Uložiť zmeny</button>
        </form>

        <a href="logout.php" class="btn-logout">Odhlásiť sa</a>
    </aside>

    <main class="profile-content">
        <h1 class="section-title">Moje rezervácie</h1>

        <div class="bookings-list" style="margin-bottom:40px; display:flex; flex-direction:column; gap:20px;">
            <?php if ($ctrl->reservations): ?>
                <?php foreach ($ctrl->reservations as $r):
                    $statusMap = [
                        'confirmed' => ['status-confirmed', 'Potvrdené'],
                        'cancelled' => ['status-cancelled', 'Zrušené'],
                    ];
                    [$cls, $txt] = $statusMap[$r['status']] ?? ['status-pending', 'Čaká na potvrdenie'];
                ?>
                <div class="booking-card">
                    <div class="booking-info">
                        <h3>Ubytovanie č. <?= ProfileController::h((string)$r['property_id']) ?></h3>
                        <p class="booking-date">📅 <?= date('d.m.Y', strtotime($r['date_from'])) ?> – <?= date('d.m.Y', strtotime($r['date_to'])) ?></p>
                        <p class="booking-status <?= $cls ?>"><?= $txt ?></p>
                    </div>
                    <a href="/Airbnb/property-details.php?id=<?= (int)$r['property_id'] ?>" class="btn-view">Zobraziť detail</a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-bookings">Zatiaľ nemáte žiadne rezervácie. Vyberte si ubytovanie na hlavnej stránke!</p>
            <?php endif; ?>
        </div>

        <h1 class="section-title">Odoslané správy</h1>

        <div class="bookings-list" style="display:flex; flex-direction:column; gap:20px;">
            <?php if ($ctrl->messages): ?>
                <?php foreach ($ctrl->messages as $m):
                    $read = ($m['status'] ?? '') === 'read';
                    $date = isset($m['created_at']) ? date('d.m.Y H:i', strtotime($m['created_at'])) : '';
                ?>
                <div class="booking-card" style="flex-direction:column; align-items:flex-start; gap:15px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; width:100%; flex-wrap:wrap; gap:10px;">
                        <div class="booking-info" style="margin-bottom:0;">
                            <h3>✉️ <?= ProfileController::h($m['subject'] ?: 'Bez predmetu') ?></h3>
                            <?php if ($date): ?><p class="booking-date">📅 <?= $date ?></p><?php endif; ?>
                            <p class="booking-status <?= $read ? 'status-confirmed' : 'status-pending' ?>" style="display:inline-block; margin-top:5px;">
                                <?= $read ? 'Prečítaná' : 'Prijatá' ?>
                            </p>
                        </div>
                        <button class="btn-view" style="border:none; cursor:pointer;"
                                onclick="var d=this.closest('.booking-card').querySelector('.msg-body'); d.style.display=d.style.display==='block'?'none':'block';">
                            Zobraziť správu
                        </button>
                    </div>
                    <div class="msg-body" style="display:none; width:100%; background:#fafafa; padding:15px; border-radius:10px; border:1px solid #f0f0f0; font-size:14px; color:#4a4a4a; line-height:1.6; box-sizing:border-box;">
                        <?= nl2br(ProfileController::h($m['message'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-bookings">Zatiaľ ste neodoslali žiadne správy cez kontaktný formulár.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
(function () {
    const trigger   = document.getElementById('edit-trigger');
    const inpName   = document.getElementById('inp-name');
    const inpPhone  = document.getElementById('inp-phone');
    const btnSave   = document.getElementById('btn-save');
    const origName  = inpName.value;
    const origPhone = inpPhone.value;
    let editing = false;

    function setEditable(on) {
        editing = on;
        [inpName, inpPhone].forEach(el => {
            el.toggleAttribute('readonly', !on);
            el.classList.toggle('unlocked', on);
        });
        trigger.classList.toggle('active', on);
        if (!on) { 
            inpName.value = origName; 
            inpPhone.value = origPhone; 
        }
        updateSave();
    }

    function updateSave() {
        if (editing && (inpName.value !== origName || inpPhone.value !== origPhone)) {
            btnSave.style.display = 'block';
        } else {
            btnSave.style.display = 'none';
        }
    }

    trigger.addEventListener('click', () => setEditable(!editing));
    inpName.addEventListener('input', updateSave);
    inpPhone.addEventListener('input', updateSave);
})();
</script>

<?php include '../partials/footer.php'; ?>