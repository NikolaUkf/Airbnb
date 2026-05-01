<?php
include 'config.php';

// Validate ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No ID provided");
}

$id = (int)$_GET['id'];

try {
    // Get image
    $stmt = $conn->prepare("SELECT image FROM properties WHERE id=?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Error: Property not found");
    }

    // Delete image
    $imageFile = $row['image'];
    $fullPath = __DIR__ . "/uploads/" . $imageFile;

    if (!empty($imageFile) && file_exists($fullPath)) {
        unlink($fullPath);
    }

    // Delete from DB
    $stmt = $conn->prepare("DELETE FROM properties WHERE id=?");
    $stmt->execute([$id]);

    // ✅ IMPORTANT: no echo before this
    header("Location: admin-dashboard.php");
    exit();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}