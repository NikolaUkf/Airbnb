<?php
session_start();
include 'config.php';

if (empty($_SESSION['admin'])) {
    header('Location: ../login_system/login.php');
    exit;
}


class PropertyRepository
{
    public function __construct(private PDO $conn) {}

    public function getAll(): array
    {
        return $this->conn->query("
            SELECT id, title, address, price, image, bedrooms, bathrooms, area, parking, type
            FROM properties
            ORDER BY id DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}


class PropertyView
{
    public function getImagePath(array $row): string
    {
        $file = $row['image'] ?? '';
        return (!empty($file) && file_exists("uploads/" . $file))
            ? "uploads/" . $file
            : "uploads/default.png";
    }

    public function getTypeLabel(string $type): string
    {
        return match($type) {
            'villa'      => 'Villa',
            'apartment'  => 'Apartmán',
            'penthouse'  => 'Penthouse',
            default      => ucfirst($type),
        };
    }
}

$repository = new PropertyRepository($conn);
$properties = $repository->getAll();
$view       = new PropertyView();


$flash = '';
if (!empty($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <title>Inzeráty | Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/sidebar.css">
    <link rel="stylesheet" href="style/read.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="top-bar">
            <h2>Správa inzerátov</h2>
            <div class="user-info">
                <div class="user-info-text">
                    <p><?php echo htmlspecialchars($_SESSION['admin']); ?></p>
                    <p>Administrator</p>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['admin'], 0, 1)); ?>
                </div>
            </div>
        </div>

        <div class="content-wrapper">

            <?php if ($flash): ?>
                <div class="message success"><?php echo htmlspecialchars($flash); ?></div>
            <?php endif; ?>

            <?php if (empty($properties)): ?>
                <div class="empty-state">
                    <p>Žiadne inzeráty zatiaľ neexistujú.</p>
                    <a href="create.php" class="btn-main">Pridať prvý inzerát</a>
                </div>
            <?php else: ?>
                <div class="properties-grid">
                    <?php foreach ($properties as $row): ?>
                        <div class="property-card">
                            <div class="image-container">
                                <img src="<?php echo htmlspecialchars($view->getImagePath($row)); ?>" alt="Property">
                                <span class="price-badge"><?php echo number_format((float)$row['price'], 0, '.', ' '); ?> €</span>
                            </div>
                            <div class="card-content">
                                <!-- OPRAVA: typ z DB namiesto natvrdo "Luxury Villa" -->
                                <div class="category-tag"><?php echo htmlspecialchars($view->getTypeLabel($row['type'])); ?></div>
                                <h3 class="property-title"><?php echo htmlspecialchars($row['address']); ?></h3>
                                <div class="specs-list">
                                    <div class="spec-item">Spálne: <strong><?php echo (int)$row['bedrooms']; ?></strong></div>
                                    <div class="spec-item">Kúpeľne: <strong><?php echo (int)$row['bathrooms']; ?></strong></div>
                                    <div class="spec-item">Plocha: <strong><?php echo (int)$row['area']; ?> m²</strong></div>
                                    <div class="spec-item">Parkovanie: <strong><?php echo (int)$row['parking']; ?></strong></div>
                                </div>
                                <div class="action-buttons">
                                    <!-- (int) zabraňuje manipulácii s ID v URL -->
                                    <a href="edit.php?id=<?php echo (int)$row['id']; ?>" class="btn-edit">Upraviť</a>
                                    <a href="delete.php?id=<?php echo (int)$row['id']; ?>" class="btn-delete"
                                       onclick="return confirm('Naozaj vymazať?')">Vymazať</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>