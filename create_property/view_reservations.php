<?php
session_start();
include 'config.php';

if (empty($_SESSION['admin'])) {
    header('Location: ../login_system/login.php');
    exit;
}

class ReservationManager
{
    private PDO $conn;

    private array $statuses = [
        'pending'   => 'Čaká',
        'confirmed' => 'Potvrdená',
        'cancelled' => 'Zrušená',
    ];

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll(): array
    {
        $stmt = $this->conn->prepare("
            SELECT r.id, r.name, r.email, r.phone, r.date_from, r.date_to,
                   r.message, r.status, r.created_at,
                   p.title, p.address
            FROM reservations r
            JOIN properties p ON r.property_id = p.id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool
    {
 
        if (!array_key_exists($status, $this->statuses)) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);


        return $stmt->rowCount() > 0;
    }

    public function getStatusLabel(string $status): string
    {
        return $this->statuses[$status] ?? htmlspecialchars($status);
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }
}

class ReservationPage
{
    private ReservationManager $manager;
    private array $reservations;
    private string $flash = '';

    public function __construct(ReservationManager $manager)
    {
        $this->manager = $manager;
        $this->handlePost();
        $this->reservations = $this->manager->getAll();
    }

    private function handlePost(): void
    {
        if (!isset($_POST['update_status'])) return;

        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $this->flash = 'error:Neplatný CSRF token.';
            return;
        }

        $id     = (int)($_POST['reservation_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if ($id <= 0) {
            $this->flash = 'error:Neplatné ID rezervácie.';
            return;
        }

        $updated = $this->manager->updateStatus($id, $status);

        $_SESSION['flash'] = $updated ? 'success:Stav bol aktualizovaný.' : 'error:Nepodarilo sa aktualizovať stav.';
        header('Location: view_reservations.php');
        exit;
    }

    private function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function getUserEmail(): string
    {
        return isset($_SESSION['admin']) ? htmlspecialchars($_SESSION['admin']) : 'Admin';
    }

    private function getUserAvatar(): string
    {
        return isset($_SESSION['admin']) ? strtoupper(substr($_SESSION['admin'], 0, 1)) : 'A';
    }

    private function renderStatusSelect(string $current, int $reservationId): string
    {
        $selectId = 'status-' . $reservationId;
        $html = "<select name=\"status\" id=\"{$selectId}\" aria-label=\"Stav rezervácie\">";
        foreach ($this->manager->getStatuses() as $value => $label) {
            $selected = $current === $value ? ' selected' : '';
            // htmlspecialchars na value aj label — obrana pred XSS
            $safeValue = htmlspecialchars($value);
            $safeLabel = htmlspecialchars($label);
            $html .= "<option value=\"{$safeValue}\"{$selected}>{$safeLabel}</option>";
        }
        $html .= '</select>';
        return $html;
    }

    private function renderFlash(): string
    {
        $msg = '';
        if (!empty($_SESSION['flash'])) {
            [$type, $text] = explode(':', $_SESSION['flash'], 2);
            unset($_SESSION['flash']);
            $safe = htmlspecialchars($text);
            $msg = "<div class=\"flash flash-{$type}\">{$safe}</div>";
        }
        return $msg;
    }

    private function renderRows(): string
    {
        if (empty($this->reservations)) {
            return '<div class="empty">Žiadne rezervácie zatiaľ neboli odoslané.</div>';
        }

        $csrf  = $this->getCsrfToken();
        $rows  = '';

        foreach ($this->reservations as $r) {
            $id          = (int)$r['id']; // int — htmlspecialchars nie je potrebný
            $statusLabel = $this->manager->getStatusLabel($r['status']);
            $statusClass = htmlspecialchars($r['status']);
            $statusSelect = $this->renderStatusSelect($r['status'], $id);

            $rows .= "
            <tr>
                <td>{$id}</td>
                <td>" . htmlspecialchars($r['title'])               . "</td>
                <td>" . htmlspecialchars($r['name'])                . "</td>
                <td>" . htmlspecialchars($r['email'])               . "</td>
                <td>" . htmlspecialchars($r['phone']    ?: '-')     . "</td>
                <td>" . htmlspecialchars($r['date_from'])           . "</td>
                <td>" . htmlspecialchars($r['date_to'])             . "</td>
                <td>" . htmlspecialchars($r['message']  ?: '-')     . "</td>
                <td><span class=\"badge badge-{$statusClass}\">{$statusLabel}</span></td>
                <td>
                    <form method=\"POST\" style=\"display:flex;gap:6px;align-items:center;\">
                        <input type=\"hidden\" name=\"reservation_id\" value=\"{$id}\">
                        <input type=\"hidden\" name=\"csrf_token\" value=\"{$csrf}\">
                        {$statusSelect}
                        <button type=\"submit\" name=\"update_status\" class=\"btn-save\">Uložiť</button>
                    </form>
                </td>
            </tr>";
        }

        return "
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Inzerát</th><th>Meno</th><th>Email</th>
                    <th>Telefón</th><th>Od</th><th>Do</th><th>Správa</th>
                    <th>Stav</th><th>Akcia</th>
                </tr>
            </thead>
            <tbody>{$rows}</tbody>
        </table>";
    }

    public function render(): void
    {
        $email  = $this->getUserEmail();
        $avatar = $this->getUserAvatar();
        $flash  = $this->renderFlash();
        $table  = $this->renderRows();

        include 'sidebar.php';

        echo "
        <div class=\"main-content\">
            <div class=\"top-bar\">
                <h2>Rezervácie</h2>
                <div class=\"user-info\">
                    <div class=\"user-info-text\">
                        <p>{$email}</p>
                        <p>Administrator</p>
                    </div>
                    <div class=\"user-avatar\">{$avatar}</div>
                </div>
            </div>
            <div class=\"content\">
                <div class=\"container\">
                    {$flash}
                    {$table}
                </div>
            </div>
        </div>";
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervácie - VILLA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/create.css">
    <link rel="stylesheet" href="style/sidebar.css">
    <link rel="stylesheet" href="style/view-reservations.css">
</head>
<body>
<?php
$page = new ReservationPage(new ReservationManager($conn));
$page->render();
?>
</body>
</html>