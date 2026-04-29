<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$query = "SELECT 
    l.id,
    l.timestamp,
    l.ip_address,
    l.event_type,
    l.action,
    l.is_suspicious,
    ls.source_name as source_type
FROM logs l
LEFT JOIN log_sources ls ON l.source_id = ls.id
ORDER BY l.timestamp DESC
LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

// Get total count
$countResult = $conn->query("SELECT COUNT(*) as total FROM logs");
$countRow = $countResult->fetch_assoc();

echo json_encode([
    'success' => true,
    'logs' => $logs,
    'total' => $countRow['total']
]);
?>
