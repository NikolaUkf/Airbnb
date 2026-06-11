<?php
session_start();

if (!isset($_SESSION['user_username'])) {
    header("Location: ../login_system/login.php");
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "villa_agency";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Spojenie zlyhalo: " . $conn->connect_error);
}

$username = $_SESSION['user_username'];
$user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : "";

$sql = "SELECT * FROM reservations WHERE email = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = 'Môj profil';
$pageStyles = ['user/user']; 

include '../partials/head.php'; 
?>

<div class="profile-container">
    <aside class="profile-card">
        <div class="avatar-section">
            <div class="avatar">👤</div>
            <h2>Ahoj, <?php echo htmlspecialchars($username); ?>!</h2>
            <p class="user-role">Člen od: 2026</p>
        </div>
        
        <hr class="divider">
        
        <div class="user-details">
            <div class="detail-item">
                <span class="label">Používateľské meno:</span>
                <span class="value"><?php echo htmlspecialchars($username); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">E-mailová adresa:</span>
                <span class="value"><?php echo htmlspecialchars($user_email); ?></span>
            </div>
        </div>
        
        <a href="../logout.php" class="btn-logout-profile">Odhlásiť sa</a>
    </aside>

    <main class="profile-content">
        <h1 class="section-title">Moje rezervácie</h1>
        
        <div class="bookings-list">
            <?php 
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $status_class = 'status-pending';
                    $status_text = 'Čaká na potvrdenie';
                    
                    if ($row['status'] == 'confirmed') {
                        $status_class = 'status-confirmed';
                        $status_text = 'Potvrdené';
                    } elseif ($row['status'] == 'cancelled') {
                        $status_class = 'status-cancelled';
                        $status_text = 'Zrušené';
                    }
                    ?>
                    <div class="booking-card">
                        <div class="booking-info">
                            <h3>Ubytovanie č. <?php echo htmlspecialchars($row['property_id']); ?></h3>
                            <p class="booking-date">📅 <?php echo date('d.m.Y', strtotime($row['date_from'])); ?> – <?php echo date('d.m.Y', strtotime($row['date_to'])); ?></p>
                            <p class="booking-status <?php echo $status_class; ?>"><?php echo $status_text; ?></p>
                        </div>
                        <a href="/Airbnb/property-details.php?id=<?php echo $row['property_id']; ?>" class="btn-view">Zobraziť detail</a>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="no-bookings">Zatiaľ nemáte žiadne rezervácie. Vyberte si ubytovanie na hlavnej stránke!</p>';
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </main>
</div>

<?php 
include '../partials/footer.php'; 
?>
</body>
</html>