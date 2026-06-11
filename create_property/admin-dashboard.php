<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login_system/login.php");
    exit();
}
include 'config.php';
class PropertyDashboardRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getTotal(): int
    {
        return (int) $this->conn->query("SELECT COUNT(*) FROM properties")->fetchColumn();
    }

    public function getAveragePrice(): float
    {
        return (float) $this->conn->query("SELECT AVG(price) FROM properties")->fetchColumn();
    }

    public function getAll(): array
    {
        return $this->conn->query("SELECT * FROM properties ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
}

class DashboardView
{
    public function getImagePath(array $row): string
    {
        $path = "uploads/" . $row['image'];
        return file_exists(__DIR__ . "/uploads/" . $row['image']) ? $path : "uploads/default.png";
    }

    public function getUserLabel(): string
    {
        return isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Používateľ';
    }

    public function getUserAvatar(): string
    {
        return isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : 'A';
    }
}

$repository = new PropertyDashboardRepository($conn);
$total      = $repository->getTotal();
$avgPrice   = $repository->getAveragePrice();
$properties = $repository->getAll();
$view       = new DashboardView();
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/sidebar.css">
    <link rel="stylesheet" href="style/dashboard.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-bar">
            <h2>Dashboard</h2>
            <div class="user-info">
                <div class="user-info-text">
                    <p><?php echo $view->getUserLabel(); ?></p>
                    <p>Administrator</p>
                </div>
                <div class="user-avatar">
                    <?php echo $view->getUserAvatar(); ?>
                </div>
            </div>
        </div>

        <div class="main">
            <div class="topbar">
                <h2>Prehľad</h2>
                <a href="create.php" class="btn-add">+ Nový inzerát</a>
            </div>

            <div class="cards">
                <div class="card">
                    <h3>Celkový počet inzerátov</h3>
                    <p><?php echo $total; ?></p>
                </div>
                <div class="card">
                    <h3>Priemerná cena</h3>
                    <p>€<?php echo number_format($avgPrice, 0, ',', ' '); ?></p>
                </div>
            </div>

            <table class="dashboard-table">
                <tr>
                    <th>Obrázok</th>
                    <th>Názov</th>
                    <th>Cena</th>
                    <th>Adresa</th>
                    <th>Akcie</th>
                </tr>
                <?php foreach ($properties as $row): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($view->getImagePath($row)); ?>"></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td>€<?php echo number_format($row['price'], 0, ',', ' '); ?></td>
                    <td><?php echo htmlspecialchars($row['address']); ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?php echo $row['id']; ?>">Upraviť</a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Vymazať tento inzerát?')">Vymazať</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>