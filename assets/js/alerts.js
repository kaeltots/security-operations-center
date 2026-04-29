// Alerts Management JavaScript

class AlertsManager {
    constructor() {
        this.currentPage = 1;
        this.pageSize = 10;
        this.init();
    }

    async init() {
        await this.loadAlerts();
    }

    async loadAlerts() {
        const status = document.getElementById('filter-status').value;
        const severity = document.getElementById('filter-severity').value;
        const type = document.getElementById('filter-type').value;
        const ip = document.getElementById('filter-ip').value;

        const params = new URLSearchParams({
            page: this.currentPage,
            limit: this.pageSize,
            status: status || '',
            severity: severity || '',
            type: type || '',
            ip: ip || ''
        });

        try {
            const response = await fetch(`/secure_web_app/api/alerts.php?${params}`);
            const data = await response.json();

            if (data.success) {
                this.displayAlerts(data.alerts);
                this.setupPagination(data.total, data.page, data.totalPages);
            }
        } catch (error) {
            console.error('Error loading alerts:', error);
        }
    }

    displayAlerts(alerts) {
        const container = document.getElementById('alerts-list');

        if (alerts.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #94a3b8;">No alerts found</p>';
            return;
        }

        container.innerHTML = alerts.map(alert => `
            <div class="alert-card ${alert.severity_level.toLowerCase()}" onclick="showAlertDetail(${alert.id})">
                <div class="alert-header">
                    <div>
                        <div class="alert-title">${alert.title}</div>
                        <div class="alert-time">${new Date(alert.detected_at).toLocaleString()}</div>
                    </div>
                    <span class="severity-badge severity-${alert.severity_level.toLowerCase()}">${alert.severity_level}</span>
                </div>

                <div class="alert-details">
                    <div class="alert-detail-item">
                        <span class="detail-label">Type:</span>
                        <span class="detail-value">${alert.alert_type}</span>
                    </div>
                    <div class="alert-detail-item">
                        <span class="detail-label">Status:</span>
                        <span class="status-badge status-${alert.status}">${this.formatStatus(alert.status)}</span>
                    </div>
                    <div class="alert-detail-item">
                        <span class="detail-label">Source IP:</span>
                        <span class="detail-value"><code>${alert.source_ip}</code></span>
                    </div>
                    ${alert.target_ip ? `
                    <div class="alert-detail-item">
                        <span class="detail-label">Target IP:</span>
                        <span class="detail-value"><code>${alert.target_ip}</code></span>
                    </div>
                    ` : ''}
                </div>

                <div class="alert-actions" onclick="event.stopPropagation();">
                    <button class="btn-action primary" onclick="updateAlertStatus(${alert.id}, 'in_review')">Review</button>
                    <button class="btn-action" onclick="updateAlertStatus(${alert.id}, 'investigating')">Investigate</button>
                    <button class="btn-action" onclick="updateAlertStatus(${alert.id}, 'resolved')">Resolve</button>
                    <button class="btn-action" onclick="updateAlertStatus(${alert.id}, 'false_positive')">False Positive</button>
                </div>
            </div>
        `).join('');
    }

    formatStatus(status) {
        const map = {
            'new': 'New',
            'in_review': 'In Review',
            'investigating': 'Investigating',
            'resolved': 'Resolved',
            'false_positive': 'False Positive'
        };
        return map[status] || status;
    }

    setupPagination(total, currentPage, totalPages) {
        const container = document.getElementById('pagination');
        let html = '';

        if (currentPage > 1) {
            html += `<a onclick="manager.goToPage(1)">« First</a>`;
            html += `<a onclick="manager.goToPage(${currentPage - 1})">‹ Prev</a>`;
        }

        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                html += `<span class="active">${i}</span>`;
            } else if (i <= 5 || i > totalPages - 5 || Math.abs(i - currentPage) <= 1) {
                html += `<a onclick="manager.goToPage(${i})">${i}</a>`;
            } else if (i === 6 && currentPage > 5) {
                html += '<span>...</span>';
            }
        }

        if (currentPage < totalPages) {
            html += `<a onclick="manager.goToPage(${currentPage + 1})">Next ›</a>`;
            html += `<a onclick="manager.goToPage(${totalPages})">Last »</a>`;
        }

        container.innerHTML = html;
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadAlerts();
        window.scrollTo(0, 0);
    }
}

// Global manager instance
let manager;

document.addEventListener('DOMContentLoaded', () => {
    manager = new AlertsManager();
});

function applyFilters() {
    manager.currentPage = 1;
    manager.loadAlerts();
}

function resetFilters() {
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-severity').value = '';
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-ip').value = '';
    manager.currentPage = 1;
    manager.loadAlerts();
}

async function updateAlertStatus(alertId, newStatus) {
    try {
        const response = await fetch('/secure_web_app/api/update-alert.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                alert_id: alertId,
                status: newStatus
            })
        });

        const data = await response.json();
        if (data.success) {
            alert(`Alert updated to ${newStatus}`);
            manager.loadAlerts();
        }
    } catch (error) {
        console.error('Error updating alert:', error);
        alert('Failed to update alert');
    }
}

function showAlertDetail(alertId) {
    window.location.href = `/secure_web_app/admin/alert-detail.php?id=${alertId}`;
}

function toggleSelectAll() {
    // Implementation for selecting all alerts
    console.log('Toggle select all');
}

function executeBulkAction() {
    const action = document.getElementById('bulk-action').value;
    if (!action) return alert('Please select an action');
    
    alert('Bulk action: ' + action);
    // Implement bulk action API call
}
