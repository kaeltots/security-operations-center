<?php
include('../includes/header.php');
include('../includes/auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerts - Security Dashboard</title>
    <link rel="stylesheet" href="/secure_web_app/assets/css/dashboard.css">
    <style>
        .alerts-container {
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

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 13px;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-filter {
            flex: 1;
            padding: 8px;
            background: #1e40af;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-filter:hover {
            background: #1e3a8a;
        }

        .btn-reset {
            flex: 1;
            padding: 8px;
            background: #94a3b8;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .alerts-list {
            display: grid;
            gap: 15px;
        }

        .alert-card {
            background: white;
            border-left: 4px solid #1e40af;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s;
        }

        .alert-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(5px);
        }

        .alert-card.critical {
            border-left-color: #dc2626;
        }

        .alert-card.high {
            border-left-color: #ea580c;
        }

        .alert-card.medium {
            border-left-color: #eab308;
        }

        .alert-card.low {
            border-left-color: #22c55e;
        }

        .alert-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .alert-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 15px;
        }

        .alert-time {
            font-size: 12px;
            color: #94a3b8;
        }

        .alert-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 10px 0;
            font-size: 13px;
        }

        .alert-detail-item {
            display: flex;
            gap: 8px;
        }

        .detail-label {
            font-weight: 600;
            color: #475569;
            min-width: 80px;
        }

        .detail-value {
            color: #0f172a;
            word-break: break-all;
        }

        .alert-actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            flex-wrap: wrap;
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

        .bulk-actions {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .pagination {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            text-decoration: none;
            color: #1e40af;
        }

        .pagination .active {
            background: #1e40af;
            color: white;
            border-color: #1e40af;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="alerts-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="nav-menu">
                <a href="/secure_web_app/admin/dashboard.php" class="nav-link">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="/secure_web_app/admin/alerts.php" class="nav-link active">
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
                <a href="/secure_web_app/admin/reports.php" class="nav-link">
                    <span class="icon">📊</span> Reports
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-bar">
                <h1>🚨 Security Alerts</h1>
                <div class="user-info">
                    <span id="current-user"></span>
                    <a href="/secure_web_app/auth/logout.php" class="btn-logout">Logout</a>
                </div>
            </header>

            <!-- Filters -->
            <div class="filter-panel">
                <h3 style="margin-bottom: 15px;">Filter Alerts</h3>
                
                <div class="filter-group">
                    <label>Status</label>
                    <select id="filter-status">
                        <option value="">All Status</option>
                        <option value="new">New</option>
                        <option value="in_review">In Review</option>
                        <option value="investigating">Investigating</option>
                        <option value="resolved">Resolved</option>
                        <option value="false_positive">False Positive</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Severity</label>
                    <select id="filter-severity">
                        <option value="">All Severities</option>
                        <option value="CRITICAL">🔴 Critical</option>
                        <option value="HIGH">🟠 High</option>
                        <option value="MEDIUM">🟡 Medium</option>
                        <option value="LOW">🟢 Low</option>
                        <option value="INFO">ℹ️ Info</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Alert Type</label>
                    <select id="filter-type">
                        <option value="">All Types</option>
                        <option value="sql_injection">SQL Injection</option>
                        <option value="xss_attempt">XSS Attempt</option>
                        <option value="path_traversal">Path Traversal</option>
                        <option value="ssh_brute_force">SSH Brute Force</option>
                        <option value="command_injection">Command Injection</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Search IP Address</label>
                    <input type="text" id="filter-ip" placeholder="192.168.1.100">
                </div>

                <div class="filter-actions">
                    <button class="btn-filter" onclick="applyFilters()">Apply Filters</button>
                    <button class="btn-reset" onclick="resetFilters()">Reset</button>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="bulk-actions">
                <label>
                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                    Select All
                </label>
                <select id="bulk-action" style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    <option value="">Bulk Actions</option>
                    <option value="mark-reviewed">Mark as In Review</option>
                    <option value="mark-investigating">Mark as Investigating</option>
                    <option value="mark-resolved">Mark as Resolved</option>
                    <option value="mark-false">Mark as False Positive</option>
                </select>
                <button class="btn-action primary" onclick="executeBulkAction()">Execute</button>
            </div>

            <!-- Alerts List -->
            <div id="alerts-list" class="alerts-list">
                <p style="text-align: center; color: #94a3b8;">Loading alerts...</p>
            </div>

            <!-- Pagination -->
            <div id="pagination" class="pagination"></div>
        </main>
    </div>

    <script src="/assets/js/alerts.js"></script>
</body>
</html>
