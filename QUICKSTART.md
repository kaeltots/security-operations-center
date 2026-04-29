# 🚀 Quick Start Guide

## Installation & Setup (5 minutes)

### Step 1: Database Setup
```bash
# Open MySQL command line or phpMyAdmin
mysql -u root -p

# Run the database setup
source setup/database.sql;
```

### Step 2: Update Database Connection
Edit `config/db.php` and ensure it matches your setup:
```php
$conn = new mysqli("localhost", "root", "", "secure_app", 3307);
```

### Step 3: Start Services
- Start Apache in XAMPP
- Start MySQL in XAMPP

### Step 4: Access Dashboard
Open browser and go to:
```
http://localhost/secure_web_app/
```

**Default Credentials:**
- Username: `admin`
- Password: *(update in database)*

---

## Key Features to Demo

### 1. Upload Sample Logs ⬆️
1. Go to **Admin → Upload Logs**
2. Select "Apache Web Server"
3. Paste sample logs:

```
192.168.1.100 - - [23/Apr/2026:14:33:22 +0000] "GET /index.php HTTP/1.1" 200 5082
192.168.1.50 - - [23/Apr/2026:14:33:25 +0000] "GET /admin.php?id=1' UNION SELECT NULL,user(),database() -- HTTP/1.1" 403 1234
10.0.0.5 - - [23/Apr/2026:14:33:30 +0000] "POST /login.php HTTP/1.1" 200 2048
```

4. Click **"Process Logs"**
5. System will detect SQL injection and create an alert! 🚨

### 2. View Real-Time Dashboard 📊
1. Go to **Dashboard**
2. See live metrics:
   - Security Score
   - Alert counts by severity
   - Charts and visualizations
   - Recent alerts table
   - IP reputation tracking

### 3. Manage Alerts 🚨
1. Go to **Alerts**
2. View detected threats
3. Click buttons to:
   - **Review** - Acknowledge alert
   - **Investigate** - Mark as investigating
   - **Resolve** - Close the alert
   - **False Positive** - Mark as incorrect detection

### 4. Create Incident 🎯
1. Go to **Incidents**
2. Click **"+ New Incident"**
3. Fill in details:
   - Title: "Potential SQL Injection Attack"
   - Description: Details about the incident
   - Severity: Critical
4. Click **"Create Incident"**
5. Incident gets assigned and tracked

### 5. Review Vulnerabilities 🔓
1. Go to **Vulnerabilities**
2. See discovered security issues
3. Filter by status and severity

---

## What's Happening Behind the Scenes

### Log Processing Pipeline
```
Upload Logs
    ↓
Parse Entries
    ↓
Apply Threat Patterns
    ↓
Generate Alerts
    ↓
Update IP Reputation
    ↓
Recalculate Security Score
```

### Threat Patterns Detected
✅ SQL Injection
✅ XSS Attempts
✅ Path Traversal
✅ SSH Brute Force
✅ Command Injection
✅ Admin Probes
✅ Failed Logins

### Security Scoring Algorithm
```
Base Score = 100
Deduct: 2 points per alert
Deduct: 5 points per critical alert
Add: 1 point per resolved alert
Range: 0-100
```

---

## File Structure Quick Reference

| File | Purpose |
|------|---------|
| `admin/dashboard.php` | Main SOC dashboard |
| `admin/alerts.php` | Alert management |
| `admin/incidents.php` | Incident response |
| `admin/upload.php` | Log upload interface |
| `includes/LogAnalyzer.php` | Threat detection engine |
| `api/upload-logs.php` | Log processing API |
| `config/db.php` | Database connection |
| `setup/database.sql` | Database schema |

---

## Interview Talking Points 💼

### When Discussing the Project:

1. **Log Analysis**
   - "I built a threat detection system that identifies SQL injection, XSS, and other OWASP-related attacks"
   - "The LogAnalyzer class uses regex patterns and semantic analysis"

2. **Security Scoring**
   - "Implemented a dynamic scoring algorithm that accounts for alert volume and resolution rate"
   - "Updates daily and influences the dashboard KPI"

3. **Database Design**
   - "Designed a normalized schema with 10+ tables for comprehensive logging"
   - "Includes audit trails, IP reputation tracking, and incident management"

4. **API Development**
   - "Built RESTful APIs for fetching metrics, alerts, and processing logs"
   - "Uses prepared statements to prevent SQL injection"

5. **User Experience**
   - "Created responsive dashboard with real-time updates"
   - "Bulk actions and advanced filtering for SOC analysts"

6. **Security Implementation**
   - "Applied OWASP best practices throughout"
   - "Input validation, output encoding, secure password hashing"

7. **Career Relevance**
   - "Combined web development and security operations skills"
   - "Shows ability to understand both sides of cybersecurity"

---

## Troubleshooting

**Database connection error?**
- Check port number (default 3307 for XAMPP)
- Verify credentials in config/db.php
- Ensure MySQL is running

**Blank dashboard?**
- Check browser console for JavaScript errors
- Verify API endpoints are working
- Check database has data

**Logs not importing?**
- Ensure log format matches expected pattern
- Check file upload size limits
- Verify database has log_sources table

---

## What Makes This Stand Out 🌟

✅ **Full Stack**: Frontend UI + Backend APIs + Database design
✅ **Real-World**: Actual security operations workflow
✅ **Professional**: Production-quality code and design
✅ **Documented**: Clear comments and README
✅ **Portfolio-Ready**: Great talking points for interviews
✅ **Scalable**: Architecture supports future enhancements

---

## Next Steps to Enhance

After you get comfortable with the basics:

1. Add data export to PDF/CSV
2. Implement email alerts
3. Add dashboard widgets customization
4. Create advanced reporting
5. Add more threat pattern signatures
6. Implement real-time log streaming
7. Add threat intelligence feed integration

---

**Good luck with your interviews! 🚀**

This project showcases both your development and security skills in a way that's immediately impressive.
