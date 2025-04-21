
<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $pdo->prepare("INSERT INTO equipment 
        (name, category_id, quantity, condition, min_stock_level, location, purchase_date, description) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $data['name'],
        $data['category_id'],
        $data['quantity'],
        $data['condition'],
        $data['min_stock_level'] ?? 5,
        $data['location'],
        $data['purchase_date'] ?? null,
        $data['description'] ?? null
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Equipment added successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>