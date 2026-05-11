<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'villa_agency');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Chyba pripojenia: " . $conn->connect_error);
}

// Získaj všetky správy
$result = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správy</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/sidebar.css">
    <link rel="stylesheet" href="style/view-messages.css">
</head>
<body>
<!--Sidebar-->
<?php include 'sidebar.php'; ?>

        <div class="main-content">
        <div class="top-bar">
            <h2>Správy z kontaktného formulára</h2>
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
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message-card">
                    <h5><?php echo htmlspecialchars($row['name']); ?> 
                        <span class="badge"><?php echo $row['status']; ?></span>
                    </h5>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Predmet:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p><strong>Správa:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    <small class="text-muted">
                        📅 <?php echo $row['created_at']; ?> 
                        | IP: <?php echo $row['ip_address']; ?>
                    </small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="alert alert-info">Zatiaľ nie sú žiadne správy.</p>
        <?php endif; ?>
    </div>
</body>
</html>