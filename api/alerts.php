<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$query = "SELECT 
    a.id,
    a.title,
    a.alert_type,
    a.source_ip,
    a.target_ip,
    a.detected_at,
    a.status,
    sl.level_name as severity_level
FROM alerts a
JOIN severity_levels sl ON a.severity_id = sl.id
WHERE a.detected_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY a.detected_at DESC
LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$alerts = [];
while ($row = $result->fetch_assoc()) {
    $alerts[] = $row;
}

echo json_encode([
    'success' => true,
    'alerts' => $alerts,
    'total' => count($alerts)
]);
?>
