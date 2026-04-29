<?php
include('../includes/header.php');
include('../includes/auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerabilities - Security Dashboard</title>
    <link rel="stylesheet" href="/secure_web_app/assets/css/dashboard.css">
    <style>
        .vuln-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-new-vuln {
            background: #dc2626;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-new-vuln:hover {
            background: #b91c1c;
        }

        .vuln-filters {
            background: white;
            padding: 15px;
            border-radius: 6px;
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .vuln-filters select {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 13px;
        }

        .vuln-table {
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .vuln-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .vuln-table thead {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .vuln-table th {
            padding: 15px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .vuln-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .vuln-table tbody tr:hover {
            background: #f8fafc;
        }

        .cve-id {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #1e40af;
        }

        .status-open {
            background: #fee2e2;
            color: #7f1d1d;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-in_progress {
            background: #cffafe;
            color: #0c4a6e;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-patched {
            background: #dcfce7;
            color: #166534;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-mitigated {
            background: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
        }

        .severity-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        .severity-critical {
            background: #dc2626;
        }

        .severity-high {
            background: #ea580c;
        }

        .severity-medium {
            background: #eab308;
            color: black;
        }

        .severity-low {
            background: #22c55e;
            color: black;
        }

        .stat-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #dc2626;
        }

        .stat-label {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 5px;
        }

        .btn-action {
            padding: 6px 12px;
            border: 1px solid #cbd5e1;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }

        .btn-action:hover {
            background: #f1f5f9;
            border-color: #1e40af;
        }

        .btn-action.primary {
            background: #1e40af;
            color: white;
            border-color: #1e40af;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="dashboard-container">
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
                <a href="/secure_web_app/admin/vulnerabilities.php" class="nav-link active">
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
                <h1>🔓 Vulnerability Management</h1>
                <div class="user-info">
                    <span id="current-user"></span>
                    <a href="/secure_web_app/auth/logout.php" class="btn-logout">Logout</a>
                </div>
            </header>

            <!-- Statistics -->
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-value" id="stat-total">0</div>
                    <div class="stat-label">Total Vulnerabilities</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-open">0</div>
                    <div class="stat-label">Open</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-critical">0</div>
                    <div class="stat-label">Critical</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-patched">0</div>
                    <div class="stat-label">Patched</div>
                </div>
            </div>

            <!-- Header -->
            <div class="vuln-header">
                <h2>Discovered Vulnerabilities</h2>
                <button class="btn-new-vuln" onclick="openNewVulnForm()">+ Report Vulnerability</button>
            </div>

            <!-- Filters -->
            <div class="vuln-filters">
                <select id="filter-status" onchange="applyFilters()">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="patched">Patched</option>
                    <option value="mitigated">Mitigated</option>
                </select>
                <select id="filter-severity" onchange="applyFilters()">
                    <option value="">All Severity</option>
                    <option value="CRITICAL">Critical</option>
                    <option value="HIGH">High</option>
                    <option value="MEDIUM">Medium</option>
                    <option value="LOW">Low</option>
                </select>
                <input type="text" id="filter-cve" placeholder="Filter by CVE..." onkeyup="applyFilters()" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>

            <!-- Vulnerabilities Table -->
            <div class="vuln-table">
                <table>
                    <thead>
                        <tr>
                            <th>CVE ID</th>
                            <th>Title</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Affected System</th>
                            <th>Discovered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="vulns-tbody">
                        <tr>
                            <td colspan="7" style="text-align: center; color: #94a3b8; padding: 40px;">Loading vulnerabilities...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        class VulnerabilitiesManager {
            constructor() {
                this.init();
            }

            async init() {
                await this.loadVulnerabilities();
            }

            async loadVulnerabilities() {
                const status = document.getElementById('filter-status').value;
                const severity = document.getElementById('filter-severity').value;
                const cve = document.getElementById('filter-cve').value;

                try {
                    const response = await fetch(`/api/vulnerabilities.php?status=${status}&severity=${severity}&cve=${cve}`);
                    const data = await response.json();

                    if (data.success) {
                        this.displayVulnerabilities(data.vulnerabilities);
                        this.updateStats(data.stats);
                    }
                } catch (error) {
                    console.error('Error loading vulnerabilities:', error);
                }
            }

            displayVulnerabilities(vulns) {
                const tbody = document.getElementById('vulns-tbody');

                if (vulns.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: #94a3b8; padding: 40px;">No vulnerabilities found</td></tr>';
                    return;
                }

                tbody.innerHTML = vulns.map(v => `
                    <tr>
                        <td><span class="cve-id">${v.cve_id || 'N/A'}</span></td>
                        <td>${v.title}</td>
                        <td><span class="severity-badge severity-${v.severity_level.toLowerCase()}">${v.severity_level}</span></td>
                        <td><span class="status-${v.status}">${this.formatStatus(v.status)}</span></td>
                        <td>${v.affected_system}</td>
                        <td>${new Date(v.discovered_date).toLocaleDateString()}</td>
                        <td>
                            <button class="btn-action primary" onclick="viewVuln(${v.id})">View</button>
                        </td>
                    </tr>
                `).join('');
            }

            formatStatus(status) {
                const map = {
                    'open': 'Open',
                    'in_progress': 'In Progress',
                    'patched': 'Patched',
                    'mitigated': 'Mitigated'
                };
                return map[status] || status;
            }

            updateStats(stats) {
                document.getElementById('stat-total').textContent = stats.total || 0;
                document.getElementById('stat-open').textContent = stats.open || 0;
                document.getElementById('stat-critical').textContent = stats.critical || 0;
                document.getElementById('stat-patched').textContent = stats.patched || 0;
            }
        }

        let manager;
        document.addEventListener('DOMContentLoaded', () => {
            manager = new VulnerabilitiesManager();
        });

        function applyFilters() {
            manager.loadVulnerabilities();
        }

        function openNewVulnForm() {
            alert('Feature coming soon - vulnerability reporting form');
        }

        function viewVuln(vulnId) {
            window.location.href = `/admin/vuln-detail.php?id=${vulnId}`;
        }
    </script>
</body>
</html>
