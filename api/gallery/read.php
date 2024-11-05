<?php
// Include database configuration
require_once '../config/db.php';

// Set header to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    // Create database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare query
    $stmt = $conn->prepare("SELECT * FROM gallery ORDER BY created_at DESC");
    $stmt->execute();

    // Fetch all results as associative array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Process results to include full image paths
    $processed_results = array_map(function($item) {
        // Add full path to image URL if needed
        $item['image_url'] = '/api/gallery-management/gallery/uploads/' . $item['image_name'];
        return $item;
    }, $result);

    // Return success response
    echo json_encode([
        'status' => 'success',
        'data' => $processed_results
    ]);

} catch(PDOException $e) {
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
}

// Close connection
$conn = null;
?>