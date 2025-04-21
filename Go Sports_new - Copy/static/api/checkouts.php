
<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // 1. Insert checkout record
    $stmt = $pdo->prepare("INSERT INTO checkouts 
        (equipment_id, quantity, borrower_name, borrower_email, due_date) 
        VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['equipment_id'],
        $data['quantity'],
        $data['borrower_name'],
        $data['borrower_email'],
        $data['due_date']
    ]);
    
    // 2. Update equipment quantity
    $stmt = $pdo->prepare("UPDATE equipment 
        SET quantity = quantity - ? 
        WHERE equipment_id = ?");
    $stmt->execute([
        $data['quantity'],
        $data['equipment_id']
    ]);
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Item checked out successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>