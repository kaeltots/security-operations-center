-- Security Monitoring Dashboard Database Schema
-- Run this SQL to set up the complete database

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'analyst', 'viewer') DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Create severity levels
CREATE TABLE IF NOT EXISTS severity_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level_name ENUM('CRITICAL', 'HIGH', 'MEDIUM', 'LOW', 'INFO') UNIQUE NOT NULL,
    score INT,
    color_code VARCHAR(7),
    description TEXT
);

INSERT INTO severity_levels (level_name, score, color_code, description) VALUES
('CRITICAL', 5, '#d32f2f', 'Critical security threat - immediate action required'),
('HIGH', 4, '#f57c00', 'High risk security event - urgent review needed'),
('MEDIUM', 3, '#fbc02d', 'Medium risk event - should be reviewed'),
('LOW', 2, '#388e3c', 'Low risk - informational'),
('INFO', 1, '#1976d2', 'Informational - no action required');

-- Create log sources (types of logs)
CREATE TABLE IF NOT EXISTS log_sources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50)
);

INSERT INTO log_sources (source_name, description, icon) VALUES
('Apache Web Server', 'HTTP server access and error logs', '🌐'),
('System Logs', 'Linux/Unix system events', '💻'),
('Firewall', 'Network firewall rules and blocks', '🔥'),
('Database', 'Database access logs', '🗄️'),
('Authentication', 'Login and authentication events', '🔑'),
('SSH', 'SSH connection attempts', '🔓'),
('Application', 'Custom application logs', '📱');

-- Create main logs table
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    source_id INT,
    user_id INT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    event_type VARCHAR(100),
    action VARCHAR(255),
    raw_log LONGTEXT,
    status VARCHAR(50),
    parsed_data JSON,
    is_suspicious BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (source_id) REFERENCES log_sources(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_timestamp (timestamp),
    INDEX idx_ip (ip_address),
    INDEX idx_suspicious (is_suspicious)
);

-- Create alerts table (derived from logs)
CREATE TABLE IF NOT EXISTS alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_id INT NOT NULL,
    alert_type VARCHAR(100),
    title VARCHAR(255),
    description TEXT,
    severity_id INT NOT NULL,
    source_ip VARCHAR(45),
    target_ip VARCHAR(45),
    detected_at DATETIME,
    acknowledged_by INT,
    acknowledged_at TIMESTAMP NULL,
    resolution_notes TEXT,
    status ENUM('new', 'in_review', 'investigating', 'resolved', 'false_positive') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (log_id) REFERENCES logs(id),
    FOREIGN KEY (severity_id) REFERENCES severity_levels(id),
    FOREIGN KEY (acknowledged_by) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_severity (severity_id),
    INDEX idx_detected_at (detected_at)
);

-- Create threat patterns table
CREATE TABLE IF NOT EXISTS threat_patterns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pattern_name VARCHAR(255),
    description TEXT,
    pattern_rule JSON,
    severity_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (severity_id) REFERENCES severity_levels(id)
);

-- Create vulnerabilities table
CREATE TABLE IF NOT EXISTS vulnerabilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cve_id VARCHAR(20),
    title VARCHAR(255),
    description TEXT,
    severity_id INT NOT NULL,
    affected_system VARCHAR(255),
    discovered_date DATE,
    remediation_steps TEXT,
    status ENUM('open', 'in_progress', 'patched', 'mitigated') DEFAULT 'open',
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (severity_id) REFERENCES severity_levels(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    INDEX idx_status (status)
);

-- Create incidents table (for tracking security incidents)
CREATE TABLE IF NOT EXISTS incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_number VARCHAR(50) UNIQUE,
    title VARCHAR(255),
    description TEXT,
    initial_alert_id INT,
    severity_id INT NOT NULL,
    status ENUM('new', 'in_progress', 'contained', 'eradicated', 'recovered', 'closed') DEFAULT 'new',
    assigned_to INT,
    started_at DATETIME,
    ended_at DATETIME NULL,
    root_cause TEXT,
    impact_assessment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (initial_alert_id) REFERENCES alerts(id),
    FOREIGN KEY (severity_id) REFERENCES severity_levels(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    INDEX idx_status (status)
);

-- Create incident timeline for tracking incident response
CREATE TABLE IF NOT EXISTS incident_timeline (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    action_type VARCHAR(100),
    description TEXT,
    performed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (incident_id) REFERENCES incidents(id),
    FOREIGN KEY (performed_by) REFERENCES users(id)
);

-- Create security metrics table (for dashboard)
CREATE TABLE IF NOT EXISTS security_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE,
    alerts_total INT DEFAULT 0,
    alerts_critical INT DEFAULT 0,
    alerts_high INT DEFAULT 0,
    alerts_medium INT DEFAULT 0,
    alerts_low INT DEFAULT 0,
    alerts_resolved INT DEFAULT 0,
    unique_source_ips INT DEFAULT 0,
    unique_events INT DEFAULT 0,
    security_score INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_date (date)
);

-- Create IP reputation table (track suspicious IPs)
CREATE TABLE IF NOT EXISTS ip_reputation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) UNIQUE,
    reputation_score INT DEFAULT 0,
    alert_count INT DEFAULT 0,
    country VARCHAR(100),
    is_blocklisted BOOLEAN DEFAULT FALSE,
    last_seen DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create audit log for tracking admin actions
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    action VARCHAR(255),
    table_name VARCHAR(100),
    record_id INT,
    old_values JSON,
    new_values JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id)
);

-- Create indexes for performance
CREATE INDEX idx_logs_timestamp ON logs(timestamp DESC);
CREATE INDEX idx_alerts_severity ON alerts(severity_id, status);
CREATE INDEX idx_incidents_status ON incidents(status);
CREATE INDEX idx_vulnerabilities_status ON vulnerabilities(status);
CREATE INDEX idx_ip_reputation_score ON ip_reputation(reputation_score DESC);

-- Create stored procedure for security scoring
DELIMITER //

CREATE PROCEDURE update_security_score(IN score_date DATE)
BEGIN
    DECLARE total_alerts INT;
    DECLARE critical_alerts INT;
    DECLARE resolved_alerts INT;
    DECLARE base_score INT;
    DECLARE final_score INT;
    
    SELECT COUNT(*) INTO total_alerts FROM alerts 
    WHERE DATE(detected_at) = score_date;
    
    SELECT COUNT(*) INTO critical_alerts FROM alerts 
    WHERE DATE(detected_at) = score_date AND severity_id = 1;
    
    SELECT COUNT(*) INTO resolved_alerts FROM alerts 
    WHERE DATE(detected_at) = score_date AND status = 'resolved';
    
    SET base_score = 100;
    SET final_score = base_score - (total_alerts * 2) - (critical_alerts * 5);
    
    IF resolved_alerts > 0 THEN
        SET final_score = final_score + (resolved_alerts);
    END IF;
    
    SET final_score = GREATEST(0, LEAST(100, final_score));
    
    INSERT INTO security_metrics 
    (date, alerts_total, alerts_critical, alerts_resolved, security_score)
    VALUES (score_date, total_alerts, critical_alerts, resolved_alerts, final_score)
    ON DUPLICATE KEY UPDATE
        alerts_total = total_alerts,
        alerts_critical = critical_alerts,
        alerts_resolved = resolved_alerts,
        security_score = final_score;
END //

DELIMITER ;

-- Insert sample user for testing
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@secure-app.local', '$2y$10$YourHashedPasswordHere', 'admin'),
('analyst', 'analyst@secure-app.local', '$2y$10$YourHashedPasswordHere', 'analyst');
