<?php
include('../includes/header.php');
include('../includes/auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidents - Security Dashboard</title>
    <link rel="stylesheet" href="/secure_web_app/assets/css/dashboard.css">
    <style>
        .incidents-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-new-incident {
            background: #16a34a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-new-incident:hover {
            background: #15803d;
        }

        .incident-card {
            background: white;
            border-left: 4px solid #1e40af;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }

        .incident-card.critical {
            border-left-color: #dc2626;
        }

        .incident-card.high {
            border-left-color: #ea580c;
        }

        .incident-number {
            display: inline-block;
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: #475569;
            margin-bottom: 10px;
        }

        .incident-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #0f172a;
        }

        .incident-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
            font-size: 13px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
        }

        .meta-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }

        .meta-value {
            color: #0f172a;
        }

        .timeline {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .timeline-item {
            display: flex;
            gap: 15px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .timeline-dot {
            width: 10px;
            height: 10px;
            background: #1e40af;
            border-radius: 50%;
            margin-top: 4px;
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-time {
            font-size: 11px;
            color: #94a3b8;
        }

        .timeline-action {
            color: #475569;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-new {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-in_progress {
            background: #cffafe;
            color: #0c4a6e;
        }

        .status-contained {
            background: #fed7aa;
            color: #92400e;
        }

        .status-eradicated {
            background: #dcfce7;
            color: #166534;
        }

        .status-recovered {
            background: #d1fae5;
            color: #065f46;
        }

        .status-closed {
            background: #e5e7eb;
            color: #374151;
        }

        .incident-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-action {
            padding: 8px 16px;
            border: 1px solid #cbd5e1;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }

        .btn-action:hover {
            background: #f1f5f9;
        }

        .btn-action.primary {
            background: #1e40af;
            color: white;
            border-color: #1e40af;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
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
            color: #1e40af;
        }

        .stat-label {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 5px;
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
                <a href="/secure_web_app/admin/incidents.php" class="nav-link active">
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
                <h1>⚠️ Incident Response</h1>
                <div class="user-info">
                    <span id="current-user"></span>
                    <a href="/secure_web_app/auth/logout.php" class="btn-logout">Logout</a>
                </div>
            </header>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" id="stat-total">0</div>
                    <div class="stat-label">Total Incidents</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-open">0</div>
                    <div class="stat-label">Open Incidents</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-investigating">0</div>
                    <div class="stat-label">Investigating</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-resolved">0</div>
                    <div class="stat-label">Resolved</div>
                </div>
            </div>

            <!-- Header with button -->
            <div class="incidents-header">
                <h2>Active Incidents</h2>
                <button class="btn-new-incident" onclick="openNewIncidentForm()">+ New Incident</button>
            </div>

            <!-- Incidents List -->
            <div id="incidents-list">
                <p style="text-align: center; color: #94a3b8; padding: 40px;">Loading incidents...</p>
            </div>
        </main>
    </div>

    <!-- New Incident Modal (hidden by default) -->
    <div id="incident-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center;">
        <div style="background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px;">
            <h2>Create New Incident</h2>
            <form onsubmit="createIncident(event)">
                <div class="form-group" style="margin-top: 20px;">
                    <label>Title</label>
                    <input type="text" id="incident-title" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea id="incident-description" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; min-height: 100px;"></textarea>
                </div>
                <div class="form-group">
                    <label>Severity</label>
                    <select id="incident-severity" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="CRITICAL">Critical</option>
                        <option value="HIGH">High</option>
                        <option value="MEDIUM">Medium</option>
                        <option value="LOW">Low</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn-action primary" style="flex: 1;">Create Incident</button>
                    <button type="button" class="btn-action" style="flex: 1;" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        class IncidentsManager {
            constructor() {
                this.init();
            }

            async init() {
                await this.loadIncidents();
            }

            async loadIncidents() {
                try {
                    const response = await fetch('/api/incidents.php');
                    const data = await response.json();

                    if (data.success) {
                        this.displayIncidents(data.incidents);
                        this.updateStats(data.stats);
                    }
                } catch (error) {
                    console.error('Error loading incidents:', error);
                }
            }

            displayIncidents(incidents) {
                const container = document.getElementById('incidents-list');

                if (incidents.length === 0) {
                    container.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 40px;">No incidents at this time</p>';
                    return;
                }

                container.innerHTML = incidents.map(incident => `
                    <div class="incident-card ${incident.severity_level.toLowerCase()}">
                        <div class="incident-number">INC-${String(incident.id).padStart(5, '0')}</div>
                        <div class="incident-title">${incident.title}</div>
                        <div class="incident-meta">
                            <div class="meta-item">
                                <span class="meta-label">Status</span>
                                <span class="status-badge status-${incident.status}">${this.formatStatus(incident.status)}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Severity</span>
                                <span class="severity-badge severity-${incident.severity_level.toLowerCase()}">${incident.severity_level}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Started</span>
                                <span class="meta-value">${new Date(incident.started_at).toLocaleString()}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Assigned To</span>
                                <span class="meta-value">${incident.assigned_to_name || 'Unassigned'}</span>
                            </div>
                        </div>

                        <div style="margin: 15px 0; color: #475569; font-size: 13px;">
                            ${incident.description}
                        </div>

                        <div class="incident-actions">
                            <button class="btn-action" onclick="updateIncidentStatus(${incident.id}, 'in_progress')">Investigate</button>
                            <button class="btn-action" onclick="updateIncidentStatus(${incident.id}, 'contained')">Contain</button>
                            <button class="btn-action" onclick="updateIncidentStatus(${incident.id}, 'resolved')">Close</button>
                            <button class="btn-action" onclick="viewIncidentDetail(${incident.id})">View Details</button>
                        </div>
                    </div>
                `).join('');
            }

            formatStatus(status) {
                const map = {
                    'new': 'New',
                    'in_progress': 'In Progress',
                    'contained': 'Contained',
                    'eradicated': 'Eradicated',
                    'recovered': 'Recovered',
                    'closed': 'Closed'
                };
                return map[status] || status;
            }

            updateStats(stats) {
                document.getElementById('stat-total').textContent = stats.total || 0;
                document.getElementById('stat-open').textContent = stats.open || 0;
                document.getElementById('stat-investigating').textContent = stats.investigating || 0;
                document.getElementById('stat-resolved').textContent = stats.resolved || 0;
            }
        }

        let manager;
        document.addEventListener('DOMContentLoaded', () => {
            manager = new IncidentsManager();
        });

        function openNewIncidentForm() {
            document.getElementById('incident-modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('incident-modal').style.display = 'none';
        }

        async function createIncident(e) {
            e.preventDefault();
            const title = document.getElementById('incident-title').value;
            const description = document.getElementById('incident-description').value;
            const severity = document.getElementById('incident-severity').value;

            try {
                const response = await fetch('/api/create-incident.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({title, description, severity})
                });
                const data = await response.json();
                if (data.success) {
                    alert('Incident created successfully');
                    closeModal();
                    manager.loadIncidents();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to create incident');
            }
        }

        async function updateIncidentStatus(incidentId, status) {
            try {
                const response = await fetch('/api/update-incident.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({incident_id: incidentId, status})
                });
                const data = await response.json();
                if (data.success) {
                    manager.loadIncidents();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function viewIncidentDetail(incidentId) {
            window.location.href = `/admin/incident-detail.php?id=${incidentId}`;
        }

        // Close modal when clicking outside
        document.addEventListener('click', (e) => {
            const modal = document.getElementById('incident-modal');
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>
</body>
</html>
