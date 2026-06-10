<?php
session_start();
include 'config.php';

if (empty($_SESSION['admin'])) {
    header('Location: ../login_system/login.php');
    exit;
}

class PropertyRepository
{
    public function __construct(private PDO $conn) {}

    public function findImageById(int $id): ?string
    {
        $stmt = $this->conn->prepare("SELECT image FROM properties WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['image'] : null;
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->conn->prepare("DELETE FROM properties WHERE id = ?");
        $stmt->execute([$id]);
    }
}

class PropertyDeleteController
{
    public function __construct(private PropertyRepository $repository) {}

    public function handle(): void
    {
        if (empty($_GET['id'])) {
            header('Location: read.php');
            exit;
        }

        $id = (int) $_GET['id'];

        try {
            $image = $this->repository->findImageById($id);

            if ($image === null) {
                header('Location: read.php');
                exit;
            }

            $fullPath = __DIR__ . "/uploads/" . $image;
            if (!empty($image) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            $this->repository->deleteById($id);

            $_SESSION['flash'] = 'Inzerát bol úspešne vymazaný.';
            header('Location: read.php');
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['flash_error'] = 'Chyba databázy. Skúste to znova.';
            header('Location: read.php');
            exit;
        }
    }
}

$controller = new PropertyDeleteController(new PropertyRepository($conn));
$controller->handle();