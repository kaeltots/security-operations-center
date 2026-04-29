<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

$status = isset($_GET['status']) && $_GET['status'] ? $_GET['status'] : '';
$severity = isset($_GET['severity']) && $_GET['severity'] ? $_GET['severity'] : '';
$cve = isset($_GET['cve']) && $_GET['cve'] ? $_GET['cve'] : '';

$query = "SELECT 
    v.id,
    v.cve_id,
    v.title,
    v.description,
    v.affected_system,
    v.discovered_date,
    v.status,
    v.remediation_steps,
    sl.level_name as severity_level,
    u.username as assigned_to_name
FROM vulnerabilities v
JOIN severity_levels sl ON v.severity_id = sl.id
LEFT JOIN users u ON v.assigned_to = u.id
WHERE 1=1";

if ($status) {
    $query .= " AND v.status = '$status'";
}

if ($severity) {
    $query .= " AND sl.level_name = '$severity'";
}

if ($cve) {
    $query .= " AND v.cve_id LIKE '%$cve%'";
}

$query .= " ORDER BY v.discovered_date DESC";

$result = $conn->query($query);
$vulns = [];

while ($row = $result->fetch_assoc()) {
    $vulns[] = $row;
}

// Get statistics
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open,
    SUM(CASE WHEN severity_id = 1 THEN 1 ELSE 0 END) as critical,
    SUM(CASE WHEN status = 'patched' THEN 1 ELSE 0 END) as patched
FROM vulnerabilities";

$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();

echo json_encode([
    'success' => true,
    'vulnerabilities' => $vulns,
    'stats' => $stats
]);
?>
