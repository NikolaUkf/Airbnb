<?php
include 'config.php';

// Fetch data safely
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
    <title>Inzeráty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/read.css">
    <link rel="stylesheet" href="style/sidebar.css">
</head>

<body>
 <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="admin-dashboard.php">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span>VILLA</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="admin-dashboard.php">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="read.php" class="active">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    <span>Inzeraty</span>
                </a>
            </li>
            <li>
                <a href="create.php">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    <span>Nový inzerát</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="logout.php">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
                Odhlásiť sa
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- TOP BAR -->
        <div class="top-bar">
            <h2>Inzeráty</h2>
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

<div class="container">

<?php if (empty($properties)): ?>
    <div class="empty">Žiadne inzeráty zatiaľ neexistujú.</div>
<?php endif; ?>

<?php foreach ($properties as $row): ?>

    <?php
    // Image handling
    $imageFile = $row['image'] ?? '';
    $filePath = __DIR__ . "/uploads/" . $imageFile;

    if (!empty($imageFile) && file_exists($filePath)) {
        $image = "uploads/" . $imageFile;
    } else {
        $image = "uploads/default.png"; // make sure this exists
    }
    ?>

<div class="card">

    <img src="<?php echo htmlspecialchars($image); ?>" class="card-img">

    <div class="card-body">

        <div class="top">
            <span class="tag">Luxury Villa</span>
            <span class="price">
                $<?php echo number_format((float)$row['price'], 0, '.', '.'); ?>
            </span>
        </div>

        <h3 class="title">
            <?php echo htmlspecialchars($row['address']); ?>
        </h3>

        <div class="info">
            <div>Bedrooms: <b><?php echo (int)$row['bedrooms']; ?></b></div>
            <div>Bathrooms: <b><?php echo (int)$row['bathrooms']; ?></b></div>
            <div>Area: <b><?php echo (int)$row['area']; ?>m²</b></div>
            <div>Floor: <b><?php echo (int)$row['floor']; ?></b></div>
            <div>Parking: <b><?php echo (int)$row['parking']; ?> spots</b></div>
        </div>

        <button class="btn">Schedule a visit</button>

    </div>

</div>

<?php endforeach; ?>

</div>

</body>
</html>