<?php
session_start();
include 'config.php';

if (empty($_SESSION['admin'])) {
    header('Location: ../login_system/login.php');
    exit;
}

class MessageManager
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

  
    public function markAllAsRead(): void
    {
        $stmt = $this->conn->prepare("UPDATE contact_messages SET status = 'read' WHERE status = 'new'");
        $stmt->execute();
    }

    public function getAll(): array
    {
        $stmt = $this->conn->prepare("
            SELECT id, name, email, subject, message, status, created_at
            FROM contact_messages
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$manager = new MessageManager($conn);


if (isset($_POST['mark_read'])) {
    $manager->markAllAsRead();
    header('Location: view_messages.php');
    exit;
}

$messages = $manager->getAll();
$unreadCount = count(array_filter($messages, fn($m) => $m['status'] === 'new'));
?>

<!DOCTYPE html>
<html lang="sk">
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
        <h2>
            Správy z kontaktného formulára
            <?php if ($unreadCount > 0): ?>
                <span class="badge badge-new"><?php echo $unreadCount; ?> nových</span>
            <?php endif; ?>
        </h2>
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

    <div class="container">

        <?php if ($unreadCount > 0): ?>
            <form method="POST" style="margin-bottom: 1rem;">
                <button type="submit" name="mark_read" class="btn-save">
                    Označiť všetky ako prečítané
                </button>
            </form>
        <?php endif; ?>

        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $row): ?>
                <div class="message-card <?php echo $row['status'] === 'new' ? 'message-card--new' : ''; ?>">
                    <h5>
                        <?php echo htmlspecialchars($row['name']); ?>
                        <span class="badge badge-<?php echo htmlspecialchars($row['status']); ?>">
                            <?php echo $row['status'] === 'new' ? 'Nová' : 'Prečítaná'; ?>
                        </span>
                    </h5>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Predmet:</strong> <?php echo htmlspecialchars($row['subject']); ?></p>
                    <p><strong>Správa:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    <small class="text-muted">
                        📅 <?php echo htmlspecialchars($row['created_at']); ?>
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