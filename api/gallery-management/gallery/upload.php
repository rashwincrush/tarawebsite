<?php
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $galleryDb = getConnection('gallery');

        // Validate input
        if (!isset($_FILES['image']) || !isset($_POST['title'])) {
            throw new Exception('Missing required fields');
        }

        // Handle file upload
        $uploadDir = '../../uploads/gallery/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $fileName;
        $imageUrl = 'uploads/gallery/' . $fileName;

        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF allowed.');
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $stmt = $galleryDb->prepare("INSERT INTO gallery (title, image_url, category) VALUES (?, ?, ?)");
            $stmt->execute([
                $_POST['title'],
                $imageUrl,
                $_POST['category'] ?? 'general'
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'id' => $galleryDb->lastInsertId(),
                    'title' => $_POST['title'],
                    'image_url' => $imageUrl
                ]
            ]);
        } else {
            throw new Exception('Failed to upload file');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
?>