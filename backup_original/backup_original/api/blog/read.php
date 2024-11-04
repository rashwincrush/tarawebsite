<?php
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Blog.php';

header('Content-Type: application/json');

$blog = new Blog();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $posts = $blog->getAll();
        echo json_encode(['success' => true, 'data' => $posts]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
