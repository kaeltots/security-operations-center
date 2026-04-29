<?php
/**
 * LogAnalyzer Class
 * Parses security logs and detects threats
 */

class LogAnalyzer {
    private $conn;
    private $threats = [];
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->loadThreatPatterns();
    }

    private function loadThreatPatterns() {
        $this->threats = [
            // SSH Attack Patterns
            'ssh_brute_force' => [
                'regex' => '/Failed password|Invalid user|authentication failure/i',
                'severity' => 'HIGH',
                'description' => 'SSH authentication failure detected'
            ],
            'ssh_root_login' => [
                'regex' => '/root@/i',
                'severity' => 'MEDIUM',
                'description' => 'SSH root login attempt'
            ],
            // SQL Injection Patterns
            'sql_injection' => [
                'regex' => "/(union|select|insert|delete|update|drop|exec|execute|script|javascript|onerror|onload|alert)\s*(from|into|where|values|set|on)/i",
                'severity' => 'CRITICAL',
                'description' => 'Possible SQL injection attempt'
            ],
            // Path Traversal
            'path_traversal' => [
                'regex' => '/\.\.\//i',
                'severity' => 'HIGH',
                'description' => 'Path traversal attempt detected'
            ],
            // Command Injection
            'command_injection' => [
                'regex' => '/[;&|`$()]/i',
                'severity' => 'CRITICAL',
                'description' => 'Command injection pattern detected'
            ],
            // XSS Patterns
            'xss_attempt' => [
                'regex' => '/<script|javascript:|onerror=|onload=|eval|onclick=/i',
                'severity' => 'HIGH',
                'description' => 'XSS attempt detected'
            ],
            // HTTP Suspicious Status Codes
            '404_scan' => [
                'regex' => '/\" 404 |\" 403 /i',
                'severity' => 'MEDIUM',
                'description' => 'Multiple 404 responses (possible scanning)'
            ],
            // Admin Panel Access
            'admin_access' => [
                'regex' => '/\/admin|\/wp-admin|\/phpmyadmin/i',
                'severity' => 'MEDIUM',
                'description' => 'Admin panel access attempt'
            ]
        ];
    }

    /**
     * Analyze a single log line
     */
    public function analyzeLog($logLine, $source) {
        $alerts = [];

        foreach ($this->threats as $threatType => $pattern) {
            if (preg_match($pattern['regex'], $logLine)) {
                $alerts[] = [
                    'type' => $threatType,
                    'severity' => $pattern['severity'],
                    'description' => $pattern['description'],
                    'log_content' => $logLine,
                    'source' => $source
                ];
            }
        }

        return $alerts;
    }

    /**
     * Parse Apache access log
     */
    public function parseApacheLog($line) {
        $pattern = '/^(\S+)\s+\S+\s+\S+\s+\[([^\]]+)\]\s+"([^"]+)"\s+(\d+)\s+(\S+)/';
        
        if (preg_match($pattern, $line, $matches)) {
            return [
                'ip' => $matches[1],
                'timestamp' => $matches[2],
                'request' => $matches[3],
                'status_code' => (int)$matches[4],
                'bytes' => $matches[5],
                'raw' => $line,
                'type' => 'HTTP_REQUEST'
            ];
        }
        return null;
    }

    /**
     * Parse SSH/System log
     */
    public function parseSystemLog($line) {
        $pattern = '/^(\w+\s+\d+\s+\d+:\d+:\d+)\s+(\S+)\s+(\w+)\[(\d+)\]:\s+(.*)$/';
        
        if (preg_match($pattern, $line, $matches)) {
            return [
                'timestamp' => $matches[1],
                'hostname' => $matches[2],
                'service' => $matches[3],
                'pid' => $matches[4],
                'message' => $matches[5],
                'raw' => $line,
                'type' => 'SYSTEM_LOG'
            ];
        }
        return null;
    }

    /**
     * Parse Firewall log
     */
    public function parseFirewallLog($line) {
        $pattern = '/(\d+\.\d+\.\d+\.\d+).*?(\d+\.\d+\.\d+\.\d+).*?(ALLOW|DENY|DROP)/i';
        
        if (preg_match($pattern, $line, $matches)) {
            return [
                'source_ip' => $matches[1],
                'dest_ip' => $matches[2],
                'action' => $matches[3],
                'raw' => $line,
                'type' => 'FIREWALL'
            ];
        }
        return null;
    }

    /**
     * Extract IP from log line
     */
    public function extractIP($line) {
        if (preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Calculate threat score based on patterns
     */
    public function calculateThreatScore($alerts) {
        $score = 0;
        $severityWeights = [
            'CRITICAL' => 50,
            'HIGH' => 20,
            'MEDIUM' => 10,
            'LOW' => 5
        ];

        foreach ($alerts as $alert) {
            $score += $severityWeights[$alert['severity']] ?? 0;
        }

        return min(100, $score);
    }
}
?>
