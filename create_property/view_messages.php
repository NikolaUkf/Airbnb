<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'villa_agency');

class MessageManager
{
    private mysqli $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            die("Chyba pripojenia: " . $this->conn->connect_error);
        }
    }

    public function markAllAsRead(): void
    {
        $this->conn->query("UPDATE contact_messages SET status = 'read' WHERE status = 'new'");
    }

    public function getAll(): array
    {
        $result = $this->conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

$manager = new MessageManager();
$manager->markAllAsRead();
$messages = $manager->getAll();
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
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $row): ?>
                <div class="message-card">
                    <h5>
                        <?php echo htmlspecialchars($row['name']); ?>
                        <span class="badge"><?php echo htmlspecialchars($row['status']); ?></span>
                    </h5>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Predmet:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p><strong>Správa:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    <small class="text-muted">
                        📅 <?php echo htmlspecialchars($row['created_at']); ?>
                        | IP: <?php echo htmlspecialchars($row['ip_address']); ?>
                    </small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="alert alert-info">Zatiaľ nie sú žiadne správy.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>