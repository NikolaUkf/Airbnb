<?php
session_start();
include 'config.php';

$message = '';
$messageType = '';

if (isset($_POST['submit'])) {

    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    $imageName = time() . "_" . basename($_FILES['image']['name']);
    $uploadPath = "uploads/" . $imageName;
    $tmp = $_FILES['image']['tmp_name'];

    if (empty($tmp)) {
        $message = "Chyba: Žiadny obrázok nebol nahratý.";
        $messageType = "error";
    } elseif (!move_uploaded_file($tmp, $uploadPath)) {
        $message = "Chyba: Nepodarilo sa nahrať obrázok.";
        $messageType = "error";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO properties 
                (title, price, address, bedrooms, bathrooms, area, floor, parking, image, type) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $result = $stmt->execute([
                $_POST['title'],
                $_POST['price'],
                $_POST['address'],
                $_POST['bedrooms'],
                $_POST['bathrooms'],
                $_POST['area'],
                $_POST['floor'],
                $_POST['parking'],
                $imageName,
                $_POST['type']
            ]);

            if ($result) {
                $message = "Inzerát bol úspešne vytvorený!";
                $messageType = "success";
                echo "<meta http-equiv='refresh' content='2;url=read.php'>";
            }
        } catch (PDOException $e) {
            $message = "Chyba databázy: " . $e->getMessage();
            $messageType = "error";
            if (file_exists($uploadPath)) {
                unlink($uploadPath);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vytvoriť inzerát - VILLA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="style/sidebar.css">
    <link rel="stylesheet" href="style/create.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
    

    <div class="main-content">
        <div class="top-bar">
            <h2>Vytvoriť nový inzerát</h2>
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
                <div class="form-card">
                    <h1>Nový inzerát</h1>

                    <?php if ($message): ?>
                        <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Názov</label>
                            <input type="text" name="title" placeholder="Názov nehnuteľnosti" required>
                        </div>
                        <div class="form-group">
                            <label>Cena</label>
                            <input type="text" name="price" placeholder="Cena za mesiac" required>
                        </div>
                        <div class="form-group">
                            <label>Adresa</label>
                            <input type="text" name="address" placeholder="Úplná adresa" required>
                        </div>
                        <div class="form-group">
                            <label>Spálne</label>
                            <input type="number" name="bedrooms" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>Kúpeľne</label>
                            <input type="number" name="bathrooms" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>Plocha (m²)</label>
                            <input type="number" name="area" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>Poschodie</label>
                            <input type="number" name="floor" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>Parkovanie</label>
                            <input type="number" name="parking" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label>Typ nehnuteľnosti</label>
                            <select name="type" required>
                                <option value="villa">Villa</option>
                                <option value="apartment">Apartmán</option>
                                <option value="penthouse">Penthouse</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Obrázok</label>
                            <div class="file-input-wrapper">
                                <input type="file" name="image" id="imageInput" accept="image/*" required>
                                <label for="imageInput" class="file-label">
                                    <span id="fileName">Klikni na nahratie obrázku</span>
                                </label>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button type="submit" name="submit" class="btn btn-submit">Uložiť inzerát</button>
                            <a href="read.php" class="btn btn-cancel">Zrušiť</a>
                        </div>
                    </form>
                </div>
        </div>
    </div>

    <script>
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Klikni na nahratie obrázku';
            document.getElementById('fileName').textContent = fileName;
        });
    </script>
</body>
</html>