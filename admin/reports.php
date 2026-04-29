<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Security Dashboard</title>
    <link rel="stylesheet" href="/secure_web_app/assets/css/dashboard.css">
    <style>
        .reports-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 0;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 0;
            grid-column: 2;
            padding: 20px;
        }

        .report-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .report-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .report-card h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .report-card p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .report-card .icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .report-card.daily {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .report-card.weekly {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .report-card.monthly {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .export-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .export-section h2 {
            margin-top: 0;
            color: #0f172a;
            margin-bottom: 20px;
        }

        .export-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .export-btn {
            padding: 15px;
            border: 2px solid #cbd5e1;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .export-btn:hover {
            background: #f1f5f9;
            border-color: #1e40af;
            color: #1e40af;
        }

        .stats-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-top: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #1e40af;
        }

        .stat-label {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/auth_check.php'); ?>

    <div class="reports-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="nav-menu">
                <a href="/secure_web_app/admin/dashboard.php" class="nav-link">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="/secure_web_app/admin/alerts.php" class="nav-link">
                    <span class="icon">🚨</span> Alerts
                </a>
                <a href="/secure_web_app/admin/logs.php" class="nav-link">
                    <span class="icon">📋</span> Logs
                </a>
                <a href="/secure_web_app/admin/incidents.php" class="nav-link">
                    <span class="icon">⚠️</span> Incidents
                </a>
                <a href="/secure_web_app/admin/vulnerabilities.php" class="nav-link">
                    <span class="icon">🔓</span> Vulnerabilities
                </a>
                <a href="/secure_web_app/admin/upload.php" class="nav-link">
                    <span class="icon">📤</span> Upload Logs
                </a>
                <a href="/secure_web_app/admin/reports.php" class="nav-link active">
                    <span class="icon">📊</span> Reports
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>📊 Security Reports</h1>
                <div class="user-info">
                    <span id="current-user"></span>
                    <a href="/secure_web_app/auth/logout.php" class="btn-logout">Logout</a>
                </div>
            </header>

            <!-- Report Generation -->
            <div class="report-cards">
                <div class="report-card" onclick="generateReport('daily')">
                    <div class="icon">📅</div>
                    <h3>Daily Report</h3>
                    <p>Today's security summary</p>
                </div>

                <div class="report-card weekly" onclick="generateReport('weekly')">
                    <div class="icon">📆</div>
                    <h3>Weekly Report</h3>
                    <p>Last 7 days summary</p>
                </div>

                <div class="report-card monthly" onclick="generateReport('monthly')">
                    <div class="icon">📈</div>
                    <h3>Monthly Report</h3>
                    <p>Last 30 days trends</p>
                </div>
            </div>

            <!-- Export Options -->
            <div class="export-section">
                <h2>📥 Export Data</h2>
                <div class="export-options">
                    <button class="export-btn" onclick="exportData('alerts-csv')">
                        <span>📋</span> Alerts (CSV)
                    </button>
                    <button class="export-btn" onclick="exportData('incidents-csv')">
                        <span>⚠️</span> Incidents (CSV)
                    </button>
                    <button class="export-btn" onclick="exportData('vulnerabilities-csv')">
                        <span>🔓</span> Vulnerabilities (CSV)
                    </button>
                    <button class="export-btn" onclick="exportData('logs-csv')">
                        <span>📋</span> Logs (CSV)
                    </button>
                    <button class="export-btn" onclick="exportData('metrics-json')">
                        <span>📊</span> Metrics (JSON)
                    </button>
                    <button class="export-btn" onclick="exportData('full-json')">
                        <span>💾</span> Full Data (JSON)
                    </button>
                </div>
            </div>

            <!-- Statistics -->
            <div class="stats-section">
                <h2>📈 Key Metrics</h2>
                <div class="stats-grid">
                    <div class="stat">
                        <div class="stat-value" id="stat-total-alerts">--</div>
                        <div class="stat-label">Total Alerts</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value" id="stat-critical-alerts">--</div>
                        <div class="stat-label">Critical Alerts</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value" id="stat-incidents">--</div>
                        <div class="stat-label">Incidents</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value" id="stat-vulns">--</div>
                        <div class="stat-label">Vulnerabilities</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value" id="stat-resolved">--</div>
                        <div class="stat-label">Resolved</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value" id="stat-score">--</div>
                        <div class="stat-label">Security Score</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        async function generateReport(period) {
            try {
                alert(`Generating ${period} report...`);
                const response = await fetch(`/api/report.php?period=${period}`);
                const data = await response.json();
                
                if (data.success) {
                    // In production, this would return a PDF
                    alert('Report generated successfully!');
                    console.log(data);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to generate report');
            }
        }

        function exportData(type) {
            alert(`Exporting ${type}...`);
            
            // Trigger download
            const endpoints = {
                'alerts-csv': '/api/export-alerts.php?format=csv',
                'incidents-csv': '/api/export-incidents.php?format=csv',
                'vulnerabilities-csv': '/api/export-vulns.php?format=csv',
                'logs-csv': '/api/export-logs.php?format=csv',
                'metrics-json': '/api/export-metrics.php?format=json',
                'full-json': '/api/export-all.php?format=json'
            };

            if (endpoints[type]) {
                window.location.href = endpoints[type];
            }
        }

        async function loadStats() {
            try {
                const response = await fetch('/api/metrics.php');
                const data = await response.json();

                if (data.success) {
                    document.getElementById('stat-total-alerts').textContent = data.metrics.total_alerts;
                    document.getElementById('stat-critical-alerts').textContent = data.metrics.critical;
                    document.getElementById('stat-resolved').textContent = data.metrics.resolved;
                    document.getElementById('stat-score').textContent = data.score;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadStats();
        });
    </script>
</body>
</html>
