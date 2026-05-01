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
    <title>Správy z kontaktného formulára</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .message-card { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #ff6b35; }
        .badge { margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Správy z kontaktného formulára</h1>
        <hr>
        
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="message-card">
                    <h5><?php echo htmlspecialchars($row['name']); ?> 
                        <span class="badge bg-info"><?php echo $row['status']; ?></span>
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