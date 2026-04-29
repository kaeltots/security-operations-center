<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

try {
    // Get incidents with statistics
    $query = "SELECT 
        i.id,
        i.incident_number,
        i.title,
        i.description,
        i.status,
        i.started_at,
        i.ended_at,
        i.root_cause,
        sl.level_name as severity_level,
        u.username as assigned_to_name
    FROM incidents i
    JOIN severity_levels sl ON i.severity_id = sl.id
    LEFT JOIN users u ON i.assigned_to = u.id
    ORDER BY i.started_at DESC";

    $result = $conn->query($query);
    $incidents = [];

    while ($row = $result->fetch_assoc()) {
        $incidents[] = $row;
    }

    // Get statistics
    $statsQuery = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'new' OR status = 'in_progress' OR status = 'contained' THEN 1 ELSE 0 END) as open,
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as investigating,
        SUM(CASE WHEN status = 'recovered' OR status = 'closed' THEN 1 ELSE 0 END) as resolved
    FROM incidents";

    $statsResult = $conn->query($statsQuery);
    $stats = $statsResult->fetch_assoc();

    echo json_encode([
        'success' => true,
        'incidents' => $incidents,
        'stats' => $stats
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
