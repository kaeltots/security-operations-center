<?php
header('Content-Type: application/json');
require_once('../config/db.php');
require_once('../includes/auth_check.php');
require_once('../includes/LogAnalyzer.php');

$analyzer = new LogAnalyzer($conn);
$processed = 0;
$alerts_created = 0;
$errors = [];

try {
    $source = isset($_POST['source']) ? $_POST['source'] : 'custom';
    
    // Get or create source ID
    $sourceStmt = $conn->prepare("SELECT id FROM log_sources WHERE source_name = ?");
    $sourceStmt->bind_param("s", $source);
    $sourceStmt->execute();
    $sourceResult = $sourceStmt->get_result();
    
    if ($sourceResult->num_rows === 0) {
        $insertSource = $conn->prepare("INSERT INTO log_sources (source_name) VALUES (?)");
        $insertSource->bind_param("s", $source);
        $insertSource->execute();
        $source_id = $conn->insert_id;
    } else {
        $sourceRow = $sourceResult->fetch_assoc();
        $source_id = $sourceRow['id'];
    }

    // Handle file upload
    if (isset($_FILES['logfile']) && $_FILES['logfile']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['logfile']['size'] > 10 * 1024 * 1024) {
            throw new Exception('File size exceeds 10MB limit');
        }
        
        $logs = file_get_contents($_FILES['logfile']['tmp_name']);
    } else if (isset($_POST['logs'])) {
        // Handle pasted text
        $logs = $_POST['logs'];
    } else {
        throw new Exception('No logs provided');
    }

    // Process each log line
    $logLines = array_filter(explode("\n", $logs));
    $userId = $_SESSION['user_id'] ?? 0;
    $severityMap = ['CRITICAL' => 1, 'HIGH' => 2, 'MEDIUM' => 3, 'LOW' => 4, 'INFO' => 5];

    foreach ($logLines as $logLine) {
        $logLine = trim($logLine);
        if (empty($logLine)) continue;

        // Insert log entry
        $ip = $analyzer->extractIP($logLine);
        $logStmt = $conn->prepare("
            INSERT INTO logs (source_id, user_id, ip_address, event_type, raw_log, status)
            VALUES (?, ?, ?, ?, ?, 'processed')
        ");
        
        $eventType = $source;
        $logStmt->bind_param("iisss", $source_id, $userId, $ip, $eventType, $logLine);
        $logStmt->execute();
        $log_id = $conn->insert_id;

        // Analyze for threats
        $detectedAlerts = $analyzer->analyzeLog($logLine, $source);

        // Create alerts
        foreach ($detectedAlerts as $alert) {
            $severity_id = $severityMap[$alert['severity']] ?? 5;
            $alertType = $alert['type'];
            $description = $alert['description'];
            $isSuspicious = $alert['severity'] !== 'INFO';

            // Insert alert
            $alertStmt = $conn->prepare("
                INSERT INTO alerts (log_id, alert_type, title, description, severity_id, source_ip, detected_at, status)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), 'new')
            ");

            $alertStmt->bind_param(
                "isssss",
                $log_id,
                $alertType,
                $alert['type'],
                $description,
                $severity_id,
                $ip
            );
            $alertStmt->execute();
            $alerts_created++;

            // Update IP reputation
            $reputationStmt = $conn->prepare("
                INSERT INTO ip_reputation (ip_address, alert_count, last_seen, reputation_score)
                VALUES (?, 1, NOW(), ?)
                ON DUPLICATE KEY UPDATE 
                    alert_count = alert_count + 1,
                    reputation_score = reputation_score + ?,
                    last_seen = NOW()
            ");

            $scoreIncrease = $severity_id <= 2 ? 30 : ($severity_id <= 3 ? 15 : 5);
            $reputationStmt->bind_param("sii", $ip, $scoreIncrease, $scoreIncrease);
            $reputationStmt->execute();
        }

        // Mark log as suspicious if alerts found
        if (count($detectedAlerts) > 0) {
            $updateStmt = $conn->prepare("UPDATE logs SET is_suspicious = TRUE WHERE id = ?");
            $updateStmt->bind_param("i", $log_id);
            $updateStmt->execute();
        }

        $processed++;
    }

    // Update daily security metrics
    $metricsQuery = "
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN severity_id = 1 THEN 1 ELSE 0 END) as critical,
            SUM(CASE WHEN severity_id = 2 THEN 1 ELSE 0 END) as high,
            SUM(CASE WHEN severity_id = 3 THEN 1 ELSE 0 END) as medium,
            SUM(CASE WHEN severity_id = 4 THEN 1 ELSE 0 END) as low,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
            COUNT(DISTINCT source_ip) as unique_ips
        FROM alerts
        WHERE DATE(detected_at) = CURDATE()
    ";

    $metricsResult = $conn->query($metricsQuery);
    $metrics = $metricsResult->fetch_assoc();

    // Calculate security score
    $baseScore = 100;
    $score = $baseScore - ($metrics['total'] * 2) - ($metrics['critical'] * 5);
    $score = max(0, min(100, $score));

    // Upsert security metrics
    $metricStmt = $conn->prepare("
        INSERT INTO security_metrics 
        (date, alerts_total, alerts_critical, alerts_high, alerts_medium, alerts_low, alerts_resolved, unique_source_ips, security_score)
        VALUES (CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            alerts_total = ?,
            alerts_critical = ?,
            alerts_high = ?,
            alerts_medium = ?,
            alerts_low = ?,
            alerts_resolved = ?,
            unique_source_ips = ?,
            security_score = ?
    ");

    $metricStmt->bind_param(
        "iiiiiiiiiiiiiiii",
        $metrics['total'], $metrics['critical'], $metrics['high'], $metrics['medium'], $metrics['low'],
        $metrics['resolved'], $metrics['unique_ips'], $score,
        $metrics['total'], $metrics['critical'], $metrics['high'], $metrics['medium'], $metrics['low'],
        $metrics['resolved'], $metrics['unique_ips'], $score
    );
    $metricStmt->execute();

    echo json_encode([
        'success' => true,
        'message' => "Processed $processed logs, created $alerts_created alerts",
        'stats' => [
            'processed' => $processed,
            'alerts_created' => $alerts_created,
            'security_score' => $score
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
