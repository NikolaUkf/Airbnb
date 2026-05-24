<?php
session_start();
include 'config.php';

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
            SELECT r.*, p.title, p.address 
            FROM reservations r 
            JOIN properties p ON r.property_id = p.id 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
    }

    public function getStatusLabel(string $status): string
    {
        return $this->statuses[$status] ?? $status;
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

    public function __construct(ReservationManager $manager)
    {
        $this->manager = $manager;
        $this->handlePost();
        $this->reservations = $this->manager->getAll();
    }

    private function handlePost(): void
    {
        if (isset($_POST['update_status'])) {
            $this->manager->updateStatus((int)$_POST['reservation_id'], $_POST['status']);
            header('Location: view_reservations.php');
            exit;
        }
    }

    private function getUserEmail(): string
    {
        return isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Používateľ';
    }

    private function getUserAvatar(): string
    {
        return isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : 'A';
    }

    private function renderStatusSelect(string $current): string
    {
        $html = '<select name="status">';
        foreach ($this->manager->getStatuses() as $value => $label) {
            $selected = $current === $value ? 'selected' : '';
            $html .= "<option value=\"{$value}\" {$selected}>{$label}</option>";
        }
        $html .= '</select>';
        return $html;
    }

    private function renderRows(): string
    {
        if (empty($this->reservations)) {
            return '<div class="empty">Žiadne rezervácie zatiaľ neboli odoslané.</div>';
        }

        $rows = '';
        foreach ($this->reservations as $r) {
            $statusLabel = $this->manager->getStatusLabel($r['status']);
            $statusSelect = $this->renderStatusSelect($r['status']);

            $rows .= "
            <tr>
                <td>{$r['id']}</td>
                <td>" . htmlspecialchars($r['title']) . "</td>
                <td>" . htmlspecialchars($r['name']) . "</td>
                <td>" . htmlspecialchars($r['email']) . "</td>
                <td>" . htmlspecialchars($r['phone'] ?: '-') . "</td>
                <td>{$r['date_from']}</td>
                <td>{$r['date_to']}</td>
                <td>" . htmlspecialchars($r['message'] ?: '-') . "</td>
                <td><span class=\"badge badge-{$r['status']}\">{$statusLabel}</span></td>
                <td>
                    <form method=\"POST\" style=\"display:flex; gap:6px; align-items:center;\">
                        <input type=\"hidden\" name=\"reservation_id\" value=\"{$r['id']}\">
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
                <div class=\"container\">{$table}</div>
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