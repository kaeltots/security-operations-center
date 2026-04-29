<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['alert_id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$alertId = (int)$data['alert_id'];
$status = $data['status'];
$userId = $_SESSION['user_id'] ?? 0;

$validStatuses = ['new', 'in_review', 'investigating', 'resolved', 'false_positive'];
if (!in_array($status, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit;
}

try {
    // Update alert status
    $stmt = $conn->prepare("UPDATE alerts SET status = ?, acknowledged_by = ?, acknowledged_at = NOW() WHERE id = ?");
    $stmt->bind_param("sii", $status, $userId, $alertId);
    $stmt->execute();

    // Log this action
    $stmt = $conn->prepare("
        INSERT INTO audit_log (admin_id, action, table_name, record_id, new_values)
        VALUES (?, ?, 'alerts', ?, JSON_OBJECT('status', ?))
    ");
    $action = "Updated alert status to $status";
    $stmt->bind_param("isis", $userId, $action, $alertId, $status);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => "Alert updated successfully"
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
