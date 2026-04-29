<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');

// Get metrics for the last 24 hours
$query = "SELECT 
    COUNT(*) as total_alerts,
    SUM(CASE WHEN severity_id = 1 THEN 1 ELSE 0 END) as critical,
    SUM(CASE WHEN severity_id = 2 THEN 1 ELSE 0 END) as high,
    SUM(CASE WHEN severity_id = 3 THEN 1 ELSE 0 END) as medium,
    SUM(CASE WHEN severity_id = 4 THEN 1 ELSE 0 END) as low,
    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
    COUNT(DISTINCT source_ip) as unique_ips
FROM alerts
WHERE detected_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";

$result = $conn->query($query);
$metrics = $result->fetch_assoc();

// Get security score
$score_query = "SELECT security_score FROM security_metrics WHERE date = CURDATE() LIMIT 1";
$score_result = $conn->query($score_query);
$score_row = $score_result->fetch_assoc();
$score = $score_row ? $score_row['security_score'] : 85;

echo json_encode([
    'success' => true,
    'metrics' => [
        'total_alerts' => (int)($metrics['total_alerts'] ?? 0),
        'critical' => (int)($metrics['critical'] ?? 0),
        'high' => (int)($metrics['high'] ?? 0),
        'medium' => (int)($metrics['medium'] ?? 0),
        'low' => (int)($metrics['low'] ?? 0),
        'resolved' => (int)($metrics['resolved'] ?? 0),
        'unique_ips' => (int)($metrics['unique_ips'] ?? 0)
    ],
    'score' => (int)$score
]);
?>
