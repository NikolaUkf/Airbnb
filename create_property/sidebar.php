<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
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
            <a href="admin-dashboard.php" <?php echo $currentPage === 'admin-dashboard.php' ? 'class="active"' : ''; ?>>
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="read.php" <?php echo $currentPage === 'read.php' ? 'class="active"' : ''; ?>>
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                <span>Inzeráty</span>
            </a>
        </li>
        <li>
            <a href="create.php" <?php echo $currentPage === 'create.php' ? 'class="active"' : ''; ?>>
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                </svg>
                <span>Nový inzerát</span>
            </a>
        </li>
        <li>
            <a href="view_messages.php" <?php echo $currentPage === 'view_messages.php' ? 'class="active"' : ''; ?>>
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                <span>Správy</span>
            </a>
        </li>
        <li>
            <a href="view_reservations.php" <?php echo $currentPage === 'view_reservations.php' ? 'class="active"' : ''; ?>>
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