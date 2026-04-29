<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Monitoring Dashboard</title>
    <link rel="stylesheet" href="/secure_web_app/assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/auth_check.php'); ?>

    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <nav class="nav-menu">
                <a href="/secure_web_app/admin/dashboard.php" class="nav-link active">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="/secure_web_app/admin/alerts.php" class="nav-link">
                    <span class="icon">🚨</span> Alerts <span class="badge badge-danger" id="alert-count">0</span>
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
                <a href="/secure_web_app/admin/reports.php" class="nav-link">
                    <span class="icon">📊</span> Reports
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>🛡️ Security Operations Center</h1>
                <div class="user-info">
                    <span id="current-user"></span>
                    <a href="/secure_web_app/auth/logout.php" class="btn-logout">Logout</a>
                </div>
            </header>

            <!-- Security Score Banner -->
            <section class="security-score-banner">
                <div class="score-circle">
                    <div class="score-value" id="security-score">--</div>
                    <div class="score-label">Security Score</div>
                </div>
                <div class="score-details">
                    <p><strong>Last 24 Hours</strong></p>
                    <p id="score-trend" class="trend"></p>
                    <p id="score-status" class="status"></p>
                </div>
            </section>

            <!-- Key Metrics Grid -->
            <section class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-icon">🚨</span>
                        <h3>Total Alerts</h3>
                    </div>
                    <div class="metric-value" id="metric-total-alerts">0</div>
                    <div class="metric-subtext">Last 24 hours</div>
                </div>

                <div class="metric-card alert-critical">
                    <div class="metric-header">
                        <span class="metric-icon">🔴</span>
                        <h3>Critical</h3>
                    </div>
                    <div class="metric-value" id="metric-critical">0</div>
                    <div class="metric-subtext">Requires immediate action</div>
                </div>

                <div class="metric-card alert-high">
                    <div class="metric-header">
                        <span class="metric-icon">🟠</span>
                        <h3>High</h3>
                    </div>
                    <div class="metric-value" id="metric-high">0</div>
                    <div class="metric-subtext">Urgent review needed</div>
                </div>

                <div class="metric-card alert-medium">
                    <div class="metric-header">
                        <span class="metric-icon">🟡</span>
                        <h3>Medium</h3>
                    </div>
                    <div class="metric-value" id="metric-medium">0</div>
                    <div class="metric-subtext">Should be reviewed</div>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-icon">✅</span>
                        <h3>Resolved</h3>
                    </div>
                    <div class="metric-value" id="metric-resolved">0</div>
                    <div class="metric-subtext">Closed alerts</div>
                </div>

                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-icon">🌐</span>
                        <h3>Unique IPs</h3>
                    </div>
                    <div class="metric-value" id="metric-unique-ips">0</div>
                    <div class="metric-subtext">Source IPs detected</div>
                </div>
            </section>

            <!-- Charts Section -->
            <section class="charts-grid">
                <div class="chart-container">
                    <h3>Alerts by Severity (24h)</h3>
                    <canvas id="severityChart"></canvas>
                </div>

                <div class="chart-container">
                    <h3>Alerts Over Time</h3>
                    <canvas id="timelineChart"></canvas>
                </div>

                <div class="chart-container">
                    <h3>Alert Status Distribution</h3>
                    <canvas id="statusChart"></canvas>
                </div>

                <div class="chart-container">
                    <h3>Top 10 Source IPs</h3>
                    <canvas id="ipChart"></canvas>
                </div>
            </section>

            <!-- Recent Alerts Table -->
            <section class="recent-alerts">
                <div class="section-header">
                    <h2>Recent Alerts</h2>
                    <a href="/secure_web_app/admin/alerts.php" class="btn-link">View All →</a>
                </div>
                <table class="alerts-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Severity</th>
                            <th>Source IP</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="recent-alerts-tbody">
                        <tr>
                            <td colspan="7" class="empty-state">Loading alerts...</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- Security Events Feed -->
            <section class="security-feed">
                <div class="section-header">
                    <h2>Latest Security Events</h2>
                </div>
                <div class="feed" id="security-feed">
                    <p class="empty-state">No recent events</p>
                </div>
            </section>
        </main>
    </div>

    <script src="/secure_web_app/assets/js/dashboard.js"></script>
</body>
</html>
