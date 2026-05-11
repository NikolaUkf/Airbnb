<?php
session_start();
include 'config.php';

if (!isset($_GET['id'])) {
    die("Chýba ID");
}

$id = (int)$_GET['id'];

// LOAD PROPERTY
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    die("Inzerát neexistuje");
}

$message = '';
$messageType = '';

if (isset($_POST['submit'])) {

    $imageName = $property['image'];

    // IMAGE UPLOAD
    if (!empty($_FILES['image']['name'])) {

        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $uploadPath = "uploads/" . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {

            $oldPath = "uploads/" . $property['image'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

        } else {
            $message = "Chyba pri nahrávaní obrázku";
            $messageType = "error";
        }
    }

    try {
        $stmt = $conn->prepare("
            UPDATE properties SET 
                title = ?,
                price = ?,
                address = ?,
                bedrooms = ?,
                bathrooms = ?,
                area = ?,
                floor = ?,
                parking = ?,
                image = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['title'],
            $_POST['price'],
            $_POST['address'],
            $_POST['bedrooms'],
            $_POST['bathrooms'],
            $_POST['area'],
            $_POST['floor'],
            $_POST['parking'],
            $imageName,
            $id
        ]);

        $message = "Inzerát bol upravený!";
        $messageType = "success";

        echo "<meta http-equiv='refresh' content='2;url=read.php'>";

    } catch (PDOException $e) {
        $message = "Chyba: " . $e->getMessage();
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="sk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editovať inzerát - VILLA</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style/create.css">
<link rel="stylesheet" href="style/sidebar.css">
<link rel="stylesheet" href="style/edit.css">
<style>
    .container {
        width: 100%;
        max-width: 800px; /* Uprav podľa šírky, ktorú máš v create.php */
        margin: 0 auto;
    }

    .form-card {
        width: 100%;
        background: #fff;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    /* Zjednotenie inputov, aby neboli príliš úzke */
    .form-group input {
        width: 100%;
        box-sizing: border-box;
    }
</style>
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
                <a href="read.php">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    <span>Inzeráty</span>
                </a>
            </li>
            <li>
                <a href="create.php" class="active">
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
                <a href="view_reservations.php" >
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

<!-- MAIN -->
<div class="main-content">

    <!-- TOP BAR -->
    <div class="top-bar">
        <h2>Editovať inzerát</h2>

        <div class="user-info">
            <div class="user-info-text">
                <p><?php echo $_SESSION['email'] ?? 'Používateľ'; ?></p>
                <p>Administrator</p>
            </div>
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['email'] ?? 'A', 0, 1)); ?>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <div class="container">
            <div class="form-card">

                <h1>Upraviť inzerát</h1>

                <?php if ($message): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>Názov</label>
                        <input type="text" name="title"
                               value="<?php echo htmlspecialchars($property['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Cena</label>
                        <input type="text" name="price"
                               value="<?php echo htmlspecialchars($property['price']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Adresa</label>
                        <input type="text" name="address"
                               value="<?php echo htmlspecialchars($property['address']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Spálne</label>
                        <input type="number" name="bedrooms"
                               value="<?php echo $property['bedrooms']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Kúpeľne</label>
                        <input type="number" name="bathrooms"
                               value="<?php echo $property['bathrooms']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Plocha (m²)</label>
                        <input type="number" name="area"
                               value="<?php echo $property['area']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Poschodie</label>
                        <input type="number" name="floor"
                               value="<?php echo $property['floor']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Parkovanie</label>
                        <input type="number" name="parking"
                               value="<?php echo $property['parking']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Aktuálny obrázok</label>
                        <img src="uploads/<?php echo $property['image']; ?>" class="preview-img">
                    </div>

                    <div class="form-group">
                        <label>Zmeniť obrázok</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="image" id="imageInput">
                            <label for="imageInput" class="file-label">
                                <span id="fileName">Vybrať nový obrázok</span>
                            </label>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" name="submit" class="btn btn-submit">
                            Uložiť zmeny
                        </button>
                        <a href="read.php" class="btn btn-cancel">
                            Zrušiť
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'Vybrať nový obrázok';
    document.getElementById('fileName').textContent = fileName;
});
</script>

</body>
</html>