<?php
// Include database configuration
require_once '../config/db.php';

// Set header to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
    exit;
}

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (!isset($data['title']) || !isset($data['description'])) {
        throw new Exception('Missing required fields');
    }

    // Create database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare INSERT query
    $stmt = $conn->prepare("
        INSERT INTO gallery (title, description, image_name, created_at)
        VALUES (:title, :description, :image_name, NOW())
    ");

    // Execute query with data
    $stmt->execute([
        ':title' => $data['title'],
        ':description' => $data['description'],
        ':image_name' => $data['image_name'] ?? null
    ]);

    // Get the inserted ID
    $newId = $conn->lastInsertId();

    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Gallery item created successfully',
        'data' => [
            'id' => $newId,
            'title' => $data['title'],
            'description' => $data['description']
        ]
    ]);

} catch(Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Close connection
$conn = null;
?>