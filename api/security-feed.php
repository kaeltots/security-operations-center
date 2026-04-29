<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

// Get recent security events from logs and alerts
$query = "SELECT 
    l.id,
    l.timestamp,
    l.event_type,
    l.action as description,
    'LOG' as source,
    CASE 
        WHEN l.is_suspicious THEN 'HIGH'
        ELSE 'INFO'
    END as severity_level
FROM logs l
WHERE l.timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
UNION ALL
SELECT 
    a.id,
    a.detected_at as timestamp,
    a.alert_type as event_type,
    a.title as description,
    'ALERT' as source,
    sl.level_name as severity_level
FROM alerts a
JOIN severity_levels sl ON a.severity_id = sl.id
WHERE a.detected_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY timestamp DESC
LIMIT ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $limit);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode([
    'success' => true,
    'events' => $events
]);
?>
