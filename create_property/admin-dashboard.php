<?php
session_start();
include 'config.php';

// STATS
$total = $conn->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$avgPrice = (float)$conn->query("SELECT AVG(price) FROM properties")->fetchColumn();

// DATA
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

<!-- SIDEBAR -->
 <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <a href="admin-dashboard.php" >
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span>VILLA</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="admin-dashboard.php" class="active">
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
<!-- MAIN -->
<div class="main">

    <!-- TOP -->
    <div class="topbar">
        <h2>Dashboard</h2>
        <a href="create.php" class="btn-add">+ Add Property</a>
    </div>

    <!-- STATS -->
    <div class="cards">
        <div class="card">
            <h3>Total Properties</h3>
            <p><?php echo $total; ?></p>
        </div>

        <div class="card">
            <h3>Average Price</h3>
            <p>€<?php echo number_format((float)$avgPrice, 0, ',', ' '); ?></p>
        </div>
    </div>

    <!-- TABLE -->
    <table>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Price</th>
            <th>Address</th>
            <th>Actions</th>
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
                <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" 
                   class="delete"
                   onclick="return confirm('Delete this property?')">
                   Delete
                </a>
            </td>
        </tr>

        <?php endforeach; ?>

    </table>

</div>

</body>
</html>