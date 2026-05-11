<?php
session_start();
include 'config.php';

$stmt = $conn->prepare("
    SELECT r.*, p.title, p.address 
    FROM reservations r 
    JOIN properties p ON r.property_id = p.id 
    ORDER BY r.created_at DESC
");
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['update_status'])) {
    $stmt2 = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt2->execute([$_POST['status'], $_POST['reservation_id']]);
    header('Location: view_reservations.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervácie - VILLA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/create.css">
    <link rel="stylesheet" href="style/sidebar.css">
    <link rel="stylesheet" href="style/view-reservations.css">

</head>
<body>
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
                <a href="read.php">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    <span>Inzeráty</span>
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
            <li>
                <a href="view_messages.php">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <span>Správy</span>
                </a>
            </li>
            <li>
                <a href="view_reservations.php" class="active">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/>
                    </svg>
                    <span>Rezervácie</span>
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

    <div class="main-content">
        <div class="top-bar">
            <h2>Rezervácie</h2>
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

        <div class="content">
            <div class="container">
                <?php if (empty($reservations)): ?>
                    <div class="empty">Žiadne rezervácie zatiaľ neboli odoslané.</div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Inzerát</th>
                            <th>Meno</th>
                            <th>Email</th>
                            <th>Telefón</th>
                            <th>Od</th>
                            <th>Do</th>
                            <th>Správa</th>
                            <th>Stav</th>
                            <th>Akcia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $r): ?>
                        <tr>
                            <td><?php echo $r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['title']); ?></td>
                            <td><?php echo htmlspecialchars($r['name']); ?></td>
                            <td><?php echo htmlspecialchars($r['email']); ?></td>
                            <td><?php echo htmlspecialchars($r['phone'] ?: '-'); ?></td>
                            <td><?php echo $r['date_from']; ?></td>
                            <td><?php echo $r['date_to']; ?></td>
                            <td><?php echo htmlspecialchars($r['message'] ?: '-'); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $r['status']; ?>">
                                    <?php
                                    $statuses = ['pending' => 'Čaká', 'confirmed' => 'Potvrdená', 'cancelled' => 'Zrušená'];
                                    echo $statuses[$r['status']];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display:flex; gap:6px; align-items:center;">
                                    <input type="hidden" name="reservation_id" value="<?php echo $r['id']; ?>">
                                    <select name="status">
                                        <option value="pending" <?php echo $r['status'] === 'pending' ? 'selected' : ''; ?>>Čaká</option>
                                        <option value="confirmed" <?php echo $r['status'] === 'confirmed' ? 'selected' : ''; ?>>Potvrdená</option>
                                        <option value="cancelled" <?php echo $r['status'] === 'cancelled' ? 'selected' : ''; ?>>Zrušená</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-save">Uložiť</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>