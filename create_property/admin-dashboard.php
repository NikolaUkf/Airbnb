<?php
session_start();
include 'config.php';

$total = $conn->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$avgPrice = (float)$conn->query("SELECT AVG(price) FROM properties")->fetchColumn();

$stmt = $conn->query("SELECT * FROM properties ORDER BY id DESC");
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <p><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Používateľ'; ?></p>
                    <p>Administrator</p>
                </div>
                <div class="user-avatar">
                    <?php echo isset($_SESSION['email']) ? strtoupper(substr($_SESSION['email'], 0, 1)) : 'A'; ?>
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
                    <p>€<?php echo number_format((float)$avgPrice, 0, ',', ' '); ?></p>
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
                <?php
                $img = "uploads/" . $row['image'];
                if (!file_exists(__DIR__ . "/uploads/" . $row['image'])) {
                    $img = "uploads/default.png";
                }
                ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($img); ?>"></td>
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