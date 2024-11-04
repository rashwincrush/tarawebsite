<?php
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Gallery.php';

header('Content-Type: application/json');

$gallery = new Gallery();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $images = $gallery->getAll();
        echo json_encode(['success' => true, 'data' => $images]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
