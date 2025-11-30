<?php
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['images']) || !is_array($data['images'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    foreach ($data['images'] as $item) {
        $id = intval($item['id']);
        $sort_order = intval($item['sort_order']);
        $conn->query("UPDATE portfolio_images SET sort_order = $sort_order WHERE id = $id");
    }
    
    echo json_encode(['success' => true, 'message' => 'Image order updated']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
