<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs - Security Dashboard</title>
    <link rel="stylesheet" href="/secure_web_app/assets/css/dashboard.css">
    <style>
        .logs-container {
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

        .filter-panel {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .logs-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .logs-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .logs-table thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .logs-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #475569;
        }

        .logs-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .logs-table tbody tr:hover {
            background: #f8fafc;
        }

        .ip-badge {
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .suspicious-badge {
            background: #fee2e2;
            color: #7f1d1d;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .normal-badge {
            background: #dcfce7;
            color: #166534;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/auth_check.php'); ?>

    <div class="logs-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="nav-menu">
                <a href="/secure_web_app/admin/dashboard.php" class="nav-link">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="/secure_web_app/admin/alerts.php" class="nav-link">
                    <span class="icon">🚨</span> Alerts
                </a>
                <a href="/secure_web_app/admin/logs.php" class="nav-link active">
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
                <h1>📋 Security Logs</h1>
                <div class="user-info">
                    <span id="current-user"></span>
                    <a href="/secure_web_app/auth/logout.php" class="btn-logout">Logout</a>
                </div>
            </header>

            <!-- Filters -->
            <div class="filter-panel">
                <h3 style="margin-bottom: 15px;">Filter Logs</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                    <div>
                        <label style="font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block;">Source</label>
                        <select id="filter-source" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                            <option value="">All Sources</option>
                            <option value="apache">Apache</option>
                            <option value="system">System</option>
                            <option value="ssh">SSH</option>
                            <option value="firewall">Firewall</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block;">Status</label>
                        <select id="filter-status" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                            <option value="">All Status</option>
                            <option value="normal">Normal</option>
                            <option value="suspicious">Suspicious</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block;">IP Address</label>
                        <input type="text" id="filter-ip" placeholder="Filter by IP..." style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="font-size: 13px; font-weight: 600; margin-bottom: 8px; display: block;">&nbsp;</label>
                        <button onclick="applyFilters()" style="width: 100%; padding: 8px; background: #1e40af; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">Filter</button>
                    </div>
                </div>
            </div>

            <!-- Logs Table -->
            <div class="logs-table">
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Source Type</th>
                            <th>IP Address</th>
                            <th>Event Type</th>
                            <th>Action/Message</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="logs-tbody">
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 40px;">Loading logs...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        class LogsManager {
            constructor() {
                this.init();
            }

            async init() {
                await this.loadLogs();
            }

            async loadLogs() {
                try {
                    const response = await fetch('/api/logs.php');
                    const data = await response.json();

                    if (data.success) {
                        this.displayLogs(data.logs);
                    }
                } catch (error) {
                    console.error('Error loading logs:', error);
                    document.getElementById('logs-tbody').innerHTML = '<tr><td colspan="6" style="text-align: center; color: red;">Error loading logs</td></tr>';
                }
            }

            displayLogs(logs) {
                const tbody = document.getElementById('logs-tbody');

                if (logs.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 40px;">No logs found</td></tr>';
                    return;
                }

                tbody.innerHTML = logs.map(log => `
                    <tr>
                        <td>${new Date(log.timestamp).toLocaleString()}</td>
                        <td>${log.source_type}</td>
                        <td><span class="ip-badge">${log.ip_address || 'N/A'}</span></td>
                        <td>${log.event_type}</td>
                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${log.action}</td>
                        <td>
                            ${log.is_suspicious ? 
                                '<span class="suspicious-badge">🚨 Suspicious</span>' : 
                                '<span class="normal-badge">Normal</span>'
                            }
                        </td>
                    </tr>
                `).join('');
            }
        }

        let manager;
        document.addEventListener('DOMContentLoaded', () => {
            manager = new LogsManager();
        });

        function applyFilters() {
            manager.loadLogs();
        }
    </script>
</body>
</html>
