<?php
session_start();
include 'config.php';


if (empty($_SESSION['admin'])) {
    header('Location: ../login_system/login.php');
    exit;
}

if (empty($_GET['id'])) {
    header('Location: read.php');
    exit;
}

$id = (int) $_GET['id'];

class PropertyRepository
{
    public function __construct(private PDO $conn) {}
    public function findById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT id, title, price, address, bedrooms, bathrooms, area, floor, parking, image
            FROM properties WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function update(int $id, array $data, string $imageName): void
    {
        $this->conn->prepare("
            UPDATE properties SET
                title = ?, price = ?, address = ?, bedrooms = ?,
                bathrooms = ?, area = ?, floor = ?, parking = ?, image = ?
            WHERE id = ?
        ")->execute([
            trim($data['title']    ?? ''),
            trim($data['price']    ?? ''),
            trim($data['address']  ?? ''),
            (int)($data['bedrooms']  ?? 0),
            (int)($data['bathrooms'] ?? 0),
            (int)($data['area']      ?? 0),
            (int)($data['floor']     ?? 0),
            (int)($data['parking']   ?? 0),
            $imageName,
            $id
        ]);
    }
}

class ImageUploader
{
    private string $uploadDir = 'uploads/';

    public function __construct()
    {
        if (!is_dir($this->uploadDir)) mkdir($this->uploadDir, 0755, true);
    }

    public function upload(array $file, string $oldImage): string
    {
        if (empty($file['name'])) return $oldImage;

        $imageName = time() . "_" . basename($file['name']);

        if (!move_uploaded_file($file['tmp_name'], $this->uploadDir . $imageName)) {
            throw new RuntimeException("Chyba pri nahrávaní obrázku.");
        }

        $oldPath = $this->uploadDir . $oldImage;
        if (file_exists($oldPath)) unlink($oldPath);

        return $imageName;
    }
}

class PropertyEditController
{
    private string $message = '';
    private string $messageType = '';

    public function __construct(
        private PropertyRepository $repository,
        private ImageUploader $uploader
    ) {}

    public function handle(int $id, array $property): void
    {
        if (!isset($_POST['submit'])) return;

        try {
            $imageName = $this->uploader->upload($_FILES['image'], $property['image']);
            $this->repository->update($id, $_POST, $imageName);
            $_SESSION['flash'] = 'Inzerát bol úspešne upravený!';
            header('Location: read.php');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $this->message     = "Chyba databázy. Skúste to znova.";
            $this->messageType = "error";
        } catch (RuntimeException $e) {
            $this->message     = $e->getMessage();
            $this->messageType = "error";
        }
    }

    public function getMessage(): string     { return $this->message; }
    public function getMessageType(): string { return $this->messageType; }
}

$repository = new PropertyRepository($conn);
$property   = $repository->findById($id);

if (!$property) {
    header('Location: read.php');
    exit;
}

$controller = new PropertyEditController($repository, new ImageUploader());
$controller->handle($id, $property);
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
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h2>Editovať inzerát</h2>
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

    <div class="content">
        <div class="container">
            <div class="form-card">
                <h1>Upraviť inzerát</h1>

                <?php if ($controller->getMessage()): ?>
                    <div class="message <?php echo $controller->getMessageType(); ?>">
                        <?php echo htmlspecialchars($controller->getMessage()); ?>
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
                               value="<?php echo (int)$property['bedrooms']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Kúpeľne</label>
                        <input type="number" name="bathrooms"
                               value="<?php echo (int)$property['bathrooms']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Plocha (m²)</label>
                        <input type="number" name="area"
                               value="<?php echo (int)$property['area']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Poschodie</label>
                        <input type="number" name="floor"
                               value="<?php echo (int)$property['floor']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Parkovanie</label>
                        <input type="number" name="parking"
                               value="<?php echo (int)$property['parking']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Aktuálny obrázok</label>
                        <img src="uploads/<?php echo htmlspecialchars($property['image']); ?>" class="preview-img">
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
                        <button type="submit" name="submit" class="btn btn-submit">Uložiť zmeny</button>
                        <a href="read.php" class="btn btn-cancel">Zrušiť</a>
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