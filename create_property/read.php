<?php
session_start();
include 'config.php';

try {
    $stmt = $conn->query("SELECT * FROM properties ORDER BY id DESC");
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Chyba databázy: " . $e->getMessage());
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
                    <p><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Používateľ'; ?></p>
                    <p>Administrator</p>
                </div>
                <div class="user-avatar">
                    <?php echo isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : 'A'; ?>
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
                    <?php foreach ($properties as $row): 
                        $imageFile = $row['image'] ?? '';
                        $image = (!empty($imageFile) && file_exists("uploads/" . $imageFile)) ? "uploads/" . $imageFile : "uploads/default.png";
                    ?>
                        <div class="property-card">
                            <div class="image-container">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Property">
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