<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['incident_id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    $incidentId = (int)$data['incident_id'];
    $status = $data['status'];
    $userId = $_SESSION['user_id'] ?? 0;

    $validStatuses = ['new', 'in_progress', 'contained', 'eradicated', 'recovered', 'closed'];
    if (!in_array($status, $validStatuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit;
    }

    // Update incident status
    $endDate = ($status === 'closed' || $status === 'recovered') ? 'NOW()' : 'NULL';
    
    $stmt = $conn->prepare("UPDATE incidents SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $incidentId);
    $stmt->execute();

    // Add timeline entry
    $stmt = $conn->prepare("
        INSERT INTO incident_timeline (incident_id, action_type, description, performed_by)
        VALUES (?, 'status_changed', ?, ?)
    ");
    $action = "Status changed to $status";
    $stmt->bind_param("isi", $incidentId, $action, $userId);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Incident updated successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
