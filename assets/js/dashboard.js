// Security Dashboard JavaScript

class SecurityDashboard {
    constructor() {
        this.refreshInterval = 30000; // Refresh every 30 seconds
        this.charts = {};
        this.init();
    }

    async init() {
        try {
            await this.loadMetrics();
            this.setupCharts();
            this.loadRecentAlerts();
            this.loadSecurityFeed();
            this.setupAutoRefresh();
        } catch (error) {
            console.error('Dashboard initialization error:', error);
        }
    }

    async loadMetrics() {
        try {
            const response = await fetch('/secure_web_app/api/metrics.php');
            const data = await response.json();

            if (data.success) {
                this.updateMetrics(data.metrics);
                this.updateSecurityScore(data.score);
            }
        } catch (error) {
            console.error('Error loading metrics:', error);
        }
    }

    updateMetrics(metrics) {
        document.getElementById('metric-total-alerts').textContent = metrics.total_alerts || 0;
        document.getElementById('metric-critical').textContent = metrics.critical || 0;
        document.getElementById('metric-high').textContent = metrics.high || 0;
        document.getElementById('metric-medium').textContent = metrics.medium || 0;
        document.getElementById('metric-resolved').textContent = metrics.resolved || 0;
        document.getElementById('metric-unique-ips').textContent = metrics.unique_ips || 0;
        document.getElementById('alert-count').textContent = metrics.total_alerts || 0;
    }

    updateSecurityScore(score) {
        const scoreElement = document.getElementById('security-score');
        const scoreStatus = document.getElementById('score-status');
        
        scoreElement.textContent = score;
        
        if (score >= 80) {
            scoreStatus.textContent = '🟢 System is secure';
            scoreStatus.style.color = '#22c55e';
        } else if (score >= 60) {
            scoreStatus.textContent = '🟡 Minor concerns detected';
            scoreStatus.style.color = '#eab308';
        } else {
            scoreStatus.textContent = '🔴 Urgent action required';
            scoreStatus.style.color = '#dc2626';
        }
    }

    setupCharts() {
        this.createSeverityChart();
        this.createTimelineChart();
        this.createStatusChart();
        this.createIPChart();
    }

    createSeverityChart() {
        const ctx = document.getElementById('severityChart').getContext('2d');
        
        this.charts.severity = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Critical', 'High', 'Medium', 'Low', 'Info'],
                datasets: [{
                    data: [5, 12, 28, 45, 20],
                    backgroundColor: [
                        '#dc2626',
                        '#ea580c',
                        '#eab308',
                        '#22c55e',
                        '#0ea5e9'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    createTimelineChart() {
        const ctx = document.getElementById('timelineChart').getContext('2d');
        
        this.charts.timeline = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'],
                datasets: [{
                    label: 'Alerts',
                    data: [12, 5, 18, 23, 15, 10, 8],
                    borderColor: '#1e40af',
                    backgroundColor: 'rgba(30, 64, 175, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#1e40af'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    createStatusChart() {
        const ctx = document.getElementById('statusChart').getContext('2d');
        
        this.charts.status = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['New', 'In Review', 'Investigating', 'Resolved', 'False Positive'],
                datasets: [{
                    data: [15, 8, 12, 40, 5],
                    backgroundColor: [
                        '#3730a3',
                        '#b45309',
                        '#0c4a6e',
                        '#166534',
                        '#6b7280'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    createIPChart() {
        const ctx = document.getElementById('ipChart').getContext('2d');
        
        this.charts.ip = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['192.168.1.5', '10.0.0.8', '172.16.0.12', '8.8.8.8', 
                        '1.1.1.1', '203.0.113.5', '198.51.100.2', '192.0.2.1',
                        '10.10.10.5', '172.31.0.3'],
                datasets: [{
                    label: 'Alert Count',
                    data: [45, 38, 32, 28, 25, 22, 18, 15, 12, 10],
                    backgroundColor: '#1e40af'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    async loadRecentAlerts() {
        try {
            const response = await fetch('/secure_web_app/api/alerts.php?limit=5');
            const data = await response.json();

            if (data.success && data.alerts.length > 0) {
                this.displayRecentAlerts(data.alerts);
            }
        } catch (error) {
            console.error('Error loading recent alerts:', error);
        }
    }

    displayRecentAlerts(alerts) {
        const tbody = document.getElementById('recent-alerts-tbody');
        
        if (alerts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="empty-state">No alerts at this time</td></tr>';
            return;
        }

        tbody.innerHTML = alerts.map(alert => `
            <tr>
                <td>${new Date(alert.detected_at).toLocaleTimeString()}</td>
                <td>${alert.alert_type}</td>
                <td><span class="severity-badge severity-${alert.severity_level.toLowerCase()}">${alert.severity_level}</span></td>
                <td><code>${alert.source_ip}</code></td>
                <td>${alert.title}</td>
                <td><span class="status-badge status-${alert.status}">${this.formatStatus(alert.status)}</span></td>
                <td><a href="/secure_web_app/admin/alert-detail.php?id=${alert.id}" class="btn-link">View</a></td>
            </tr>
        `).join('');
    }

    async loadSecurityFeed() {
        try {
            const response = await fetch('/secure_web_app/api/security-feed.php?limit=10');
            const data = await response.json();

            if (data.success && data.events.length > 0) {
                this.displaySecurityFeed(data.events);
            }
        } catch (error) {
            console.error('Error loading security feed:', error);
        }
    }

    displaySecurityFeed(events) {
        const feed = document.getElementById('security-feed');
        
        if (events.length === 0) {
            feed.innerHTML = '<p class="empty-state">No recent events</p>';
            return;
        }

        feed.innerHTML = events.map(event => {
            const severityClass = event.severity_level ? event.severity_level.toLowerCase() : 'info';
            return `
                <div class="feed-item ${severityClass}">
                    <strong>${event.event_type || 'Security Event'}</strong><br>
                    ${event.description || event.title}<br>
                    <span class="feed-time">${new Date(event.timestamp).toLocaleString()}</span>
                </div>
            `;
        }).join('');
    }

    formatStatus(status) {
        const statusMap = {
            'new': 'New',
            'in_review': 'In Review',
            'investigating': 'Investigating',
            'resolved': 'Resolved',
            'false_positive': 'False Positive'
        };
        return statusMap[status] || status;
    }

    setupAutoRefresh() {
        setInterval(() => this.loadMetrics(), this.refreshInterval);
        setInterval(() => this.loadRecentAlerts(), this.refreshInterval);
        setInterval(() => this.loadSecurityFeed(), this.refreshInterval);
    }
}

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new SecurityDashboard();
});
