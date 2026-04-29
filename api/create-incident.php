<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['title']) || !isset($data['severity'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

try {
    $userId = $_SESSION['user_id'] ?? 0;
    $title = $data['title'];
    $description = $data['description'] ?? '';
    $severity = $data['severity'];

    // Map severity to ID
    $severityMap = ['CRITICAL' => 1, 'HIGH' => 2, 'MEDIUM' => 3, 'LOW' => 4];
    $severity_id = $severityMap[$severity] ?? 3;

    // Generate incident number
    $incidentNum = 'INC-' . date('Ymd') . '-' . substr(uniqid(), -4);

    // Create incident
    $stmt = $conn->prepare("
        INSERT INTO incidents 
        (incident_number, title, description, severity_id, assigned_to, started_at, status)
        VALUES (?, ?, ?, ?, ?, NOW(), 'new')
    ");

    $stmt->bind_param("sssii", $incidentNum, $title, $description, $severity_id, $userId);
    $stmt->execute();
    $incident_id = $conn->insert_id;

    // Log timeline entry
    $stmt = $conn->prepare("
        INSERT INTO incident_timeline (incident_id, action_type, description, performed_by)
        VALUES (?, 'created', 'Incident created', ?)
    ");
    $stmt->bind_param("ii", $incident_id, $userId);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'incident_id' => $incident_id,
        'incident_number' => $incidentNum
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
