<?php
header('Content-Type: application/json');
require_once '../config/database.php';

try {
    // Get all stats in one query
    $stats = [];
    
    // Total items
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM equipment");
    $stats['total_items'] = $stmt->fetchColumn();
    
    // Checked out items
    $stmt = $pdo->query("SELECT SUM(quantity) as total FROM checkouts WHERE status = 'Checked Out'");
    $stats['checked_out'] = $stmt->fetchColumn() ?? 0;
    
    // Low stock items
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM equipment WHERE quantity <= min_stock_level");
    $stats['low_stock'] = $stmt->fetchColumn();
    
    // Overdue items
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM checkouts 
                         WHERE status = 'Checked Out' AND due_date < CURDATE()");
    $stats['overdue'] = $stmt->fetchColumn();
    
    // Recent activity (last 5 activities)
    $stmt = $pdo->query("
        (SELECT 'checkout' as type, CONCAT(borrower_name, ' checked out ', quantity, ' ', e.name) as description, 
         checkout_date as timestamp 
         FROM checkouts c JOIN equipment e ON c.equipment_id = e.equipment_id 
         ORDER BY checkout_date DESC LIMIT 3)
        UNION
        (SELECT 'addition' as type, CONCAT('Admin added ', quantity, ' new ', name) as description, 
         last_updated as timestamp 
         FROM equipment 
         ORDER BY last_updated DESC LIMIT 2)
        ORDER BY timestamp DESC LIMIT 5
    ");
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format activities for display
    $stats['recent_activity'] = array_map(function($activity) {
        return [
            'description' => $activity['description'],
            'time' => time_elapsed_string($activity['timestamp']),
            'icon' => $activity['type'] === 'checkout' ? 'upload' : 'plus'
        ];
    }, $activities);
    
    echo json_encode($stats);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second'
    ];
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>