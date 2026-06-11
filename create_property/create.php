<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login_system/login.php");
    exit();
}
include 'config.php';
class ImageUploader
{
    private string $uploadDir = 'uploads/';

    public function __construct()
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(array $file): string
    {
        if (empty($file['tmp_name'])) {
            throw new RuntimeException("Chyba: Žiadny obrázok nebol nahratý.");
        }

        $imageName  = time() . "_" . basename($file['name']);
        $uploadPath = $this->uploadDir . $imageName;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new RuntimeException("Chyba: Nepodarilo sa nahrať obrázok.");
        }

        return $imageName;
    }

    public function delete(string $imageName): void
    {
        $path = $this->uploadDir . $imageName;
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

class PropertyCreateRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create(array $data, string $imageName): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO properties 
            (title, price, address, bedrooms, bathrooms, area, floor, parking, image, type) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        return $stmt->execute([
            $data['title'],
            $data['price'],
            $data['address'],
            $data['bedrooms'],
            $data['bathrooms'],
            $data['area'],
            $data['floor'],
            $data['parking'],
            $imageName,
            $data['type'],
        ]);
    }
}

class PropertyCreateController
{
    private ImageUploader $uploader;
    private PropertyCreateRepository $repository;
    private string $message = '';
    private string $messageType = '';

    public function __construct(ImageUploader $uploader, PropertyCreateRepository $repository)
    {
        $this->uploader   = $uploader;
        $this->repository = $repository;
    }

    public function handle(): void
    {
        if (!isset($_POST['submit'])) return;

        try {
            $imageName = $this->uploader->upload($_FILES['image']);
            $this->repository->create($_POST, $imageName);
            $this->message     = "Inzerát bol úspešne vytvorený!";
            $this->messageType = "success";
            echo "<meta http-equiv='refresh' content='2;url=read.php'>";
        } catch (PDOException $e) {
            $this->message     = "Chyba databázy: " . $e->getMessage();
            $this->messageType = "error";
            if (isset($imageName)) {
                $this->uploader->delete($imageName);
            }
        } catch (RuntimeException $e) {
            $this->message     = $e->getMessage();
            $this->messageType = "error";
        }
    }

    public function getMessage(): string { return $this->message; }
    public function getMessageType(): string { return $this->messageType; }
}

$controller = new PropertyCreateController(new ImageUploader(), new PropertyCreateRepository($conn));
$controller->handle();
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

                <?php if ($controller->getMessage()): ?>
                    <div class="message <?php echo $controller->getMessageType(); ?>"><?php echo $controller->getMessage(); ?></div>
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