<?php
include 'config.php';

class PropertyDeleteRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

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
    private PropertyDeleteRepository $repository;

    public function __construct(PropertyDeleteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(): void
    {
        if (empty($_GET['id'])) {
            die("Error: No ID provided");
        }

        $id = (int) $_GET['id'];

        try {
            $image = $this->repository->findImageById($id);

            if ($image === null) {
                die("Error: Property not found");
            }

            $fullPath = __DIR__ . "/uploads/" . $image;
            if (!empty($image) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            $this->repository->deleteById($id);

            header("Location: admin-dashboard.php");
            exit();

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
}

$controller = new PropertyDeleteController(new PropertyDeleteRepository($conn));
$controller->handle();