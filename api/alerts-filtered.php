<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$status = isset($_GET['status']) && $_GET['status'] ? $_GET['status'] : null;
$severity = isset($_GET['severity']) && $_GET['severity'] ? $_GET['severity'] : null;
$type = isset($_GET['type']) && $_GET['type'] ? $_GET['type'] : null;
$ip = isset($_GET['ip']) && $_GET['ip'] ? $_GET['ip'] : null;

// Build query
$query = "SELECT 
    a.id,
    a.title,
    a.alert_type,
    a.source_ip,
    a.target_ip,
    a.detected_at,
    a.status,
    a.description,
    sl.level_name as severity_level
FROM alerts a
JOIN severity_levels sl ON a.severity_id = sl.id
WHERE 1=1";

$countQuery = "SELECT COUNT(*) as total FROM alerts a WHERE 1=1";

if ($status) {
    $query .= " AND a.status = '$status'";
    $countQuery .= " AND a.status = '$status'";
}

if ($severity) {
    $query .= " AND sl.level_name = '$severity'";
    $countQuery .= " AND sl.level_name = '$severity'";
}

if ($type) {
    $query .= " AND a.alert_type = '$type'";
    $countQuery .= " AND a.alert_type = '$type'";
}

if ($ip) {
    $query .= " AND (a.source_ip LIKE '%$ip%' OR a.target_ip LIKE '%$ip%')";
    $countQuery .= " AND (a.source_ip LIKE '%$ip%' OR a.target_ip LIKE '%$ip%')";
}

$query .= " ORDER BY a.detected_at DESC LIMIT $limit OFFSET $offset";

$result = $conn->query($query);
$countResult = $conn->query($countQuery);
$countRow = $countResult->fetch_assoc();
$total = $countRow['total'];

$alerts = [];
while ($row = $result->fetch_assoc()) {
    $alerts[] = $row;
}

$totalPages = ceil($total / $limit);

echo json_encode([
    'success' => true,
    'alerts' => $alerts,
    'total' => $total,
    'page' => $page,
    'limit' => $limit,
    'totalPages' => $totalPages
]);
?>
