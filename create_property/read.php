<?php
session_start();
include 'config.php';

class PropertyReadRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll(): array
    {
        try {
            return $this->conn->query("SELECT * FROM properties ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Chyba databázy: " . $e->getMessage());
        }
    }
}

class PropertyReadView
{
    public function getImagePath(array $row): string
    {
        $file = $row['image'] ?? '';
        return (!empty($file) && file_exists("uploads/" . $file)) ? "uploads/" . $file : "uploads/default.png";
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

$repository = new PropertyReadRepository($conn);
$properties = $repository->getAll();
$view       = new PropertyReadView();
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
                    <p><?php echo $view->getUserLabel(); ?></p>
                    <p>Administrator</p>
                </div>
                <div class="user-avatar">
                    <?php echo $view->getUserAvatar(); ?>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
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
                                <div class="category-tag">Luxury Villa</div>
                                <h3 class="property-title"><?php echo htmlspecialchars($row['address']); ?></h3>
                                <div class="specs-list">
                                    <div class="spec-item">Spálne: <strong><?php echo (int)$row['bedrooms']; ?></strong></div>
                                    <div class="spec-item">Kúpeľne: <strong><?php echo (int)$row['bathrooms']; ?></strong></div>
                                    <div class="spec-item">Plocha: <strong><?php echo (int)$row['area']; ?> m²</strong></div>
                                    <div class="spec-item">Parkovanie: <strong><?php echo (int)$row['parking']; ?></strong></div>
                                </div>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-edit">Upraviť</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Naozaj vymazať?')">Vymazať</a>
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