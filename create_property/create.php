<?php
session_start();
include 'config.php';
?>
<?php
// Keď prijmeš POST dáta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = trim($_POST['title'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $address = trim($_POST['address'] ?? '');
    // ... ostatné polia
    
    // VALIDÁCIA CENY
    $errors = [];
    
    // 1. Skontroluj či je prázdne
    if (empty($price)) {
        $errors[] = 'Cena je povinná!';
    } 
    // 2. Skontroluj či je číslo
    elseif (!is_numeric($price)) {
        $errors[] = 'Cena musí byť číslo!';
    } 
    // 3. Konvertuj na float a skontroluj
    else {
        $price = (float)$price;
        
        // 4. Cena nesmie byť záporná alebo 0
        if ($price <= 0) {
            $errors[] = 'Cena musí byť väčšia ako 0!';
        }
        
        // 5. Cena nesmie presiahnuť rozumný limit
        if ($price > 10000000) {
            $errors[] = 'Cena je príliš vysoká! (Maximum: €10,000,000)';
        }
        
        // 6. Skontroluj počet desatinných miest
        if (strlen(substr(strrchr($price, "."), 1)) > 2) {
            $errors[] = 'Maximálne 2 desatinné miesta!';
        }
    }
    
    // Ak sú chyby, zastaví sa tu
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }
    
    // VKLADANIE DO DATABÁZY (len ak je všetko OK)
    try {
        $stmt = $conn->prepare("INSERT INTO properties (title, price, address, ...) VALUES (?, ?, ?, ...)");
        $stmt->execute([$title, $price, $address, ...]);
        
        echo json_encode(['success' => true, 'message' => 'Property bolo úspešne vytvorené!']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Chyba pri ukladaní!']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vytvoriť inzerát - VILLA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style/create.css">
    <link rel="stylesheet" href="style/sidebar.css">
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
                    <span>Inzeraty</span>
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

        <!-- FORM CONTENT -->
        <div class="content">
            <div class="container">
                <div class="form-card">
                    <h1>Nový inzerát</h1>

                    <?php
                    $message = '';
                    $messageType = '';

                    if (isset($_POST['submit'])) {
                        
                        // Create uploads directory if it doesn't exist
                        if (!is_dir('uploads')) {
                            mkdir('uploads', 0755, true);
                        }

                        // Handle image upload
                        $imageName = time() . "_" . basename($_FILES['image']['name']);
                        $uploadPath = "uploads/" . $imageName;
                        $tmp = $_FILES['image']['tmp_name'];
                        
                        // Validate file upload
                        if (!move_uploaded_file($tmp, $uploadPath)) {
                            $message = "Chyba: Nepodarilo sa nahrať obrázok. Skontroluj oprávnenia.";
                            $messageType = "error";
                        } else {
                            // Insert into database with error handling
                            try {
                                $stmt = $conn->prepare("INSERT INTO properties 
                                    (title, price, address, bedrooms, bathrooms, area, floor, parking, image) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                                $result = $stmt->execute([
                                    $_POST['title'],
                                    $_POST['price'],
                                    $_POST['address'],
                                    $_POST['bedrooms'],
                                    $_POST['bathrooms'],
                                    $_POST['area'],
                                    $_POST['floor'],
                                    $_POST['parking'],
                                    $imageName
                                ]);

                                if ($result) {
                                    $message = "Inzerát bol úspešne vytvorený!";
                                    $messageType = "success";
                                    // Redirect after 2 seconds
                                    echo "<meta http-equiv='refresh' content='2;url=admina/prop.php'>";
                                }
                            } catch (PDOException $e) {
                                $message = "Chyba databázy: " . $e->getMessage();
                                $messageType = "error";
                                // If DB insert fails, delete the uploaded image
                                if (file_exists($uploadPath)) {
                                    unlink($uploadPath);
                                }
                            }
                        }
                    }

                    if ($message) {
                        echo "<div class='message $messageType'>$message</div>";
                    }
                    ?>

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
                            <a href="admina/prop.php" class="btn btn-cancel">Zrušiť</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update file name display
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Klikni na nahratie obrázku';
            document.getElementById('fileName').textContent = fileName;
        });
    </script>
</body>
</html>