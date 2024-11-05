<?php
require_once '../config/db.php';

header('Content-Type: application/json');

try {
    $galleryDb = getConnection('gallery');

    // Get category filter if provided
    $category = $_GET['category'] ?? null;

    if ($category && $category !== 'all') {
        $stmt = $galleryDb->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY created_at DESC");
        $stmt->execute([$category]);
    } else {
        $stmt = $galleryDb->query("SELECT * FROM gallery ORDER BY created_at DESC");
    }

    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $images
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>