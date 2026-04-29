0# 🛡️ Security Monitoring Dashboard

A comprehensive **Security Operations Center (SOC) platform** built with PHP and MySQL, designed to help junior security analysts monitor, analyze, and respond to security threats in real-time.

## 🎯 Project Overview

This is a full-stack web application that demonstrates both **junior web developer** and **junior SOC analyst** skills. It showcases:

- **Web Development**: Responsive UI, RESTful APIs, database design, authentication
- **Security Operations**: Threat detection, log analysis, incident response, security scoring
- **Security Best Practices**: Secure coding, input validation, OWASP protection

## ✨ Key Features

### 1. **Real-Time Security Dashboard**
- Live security metrics and KPIs
- Security score calculation (0-100)
- Alert severity breakdown (Critical, High, Medium, Low)
- Visual charts using Chart.js
- Mobile-responsive design

### 2. **Intelligent Log Analysis**
- Support for multiple log sources:
  - Apache Web Server logs
  - System logs (Linux/Unix)
  - Firewall logs
  - SSH logs
  - Database logs
  - Custom application logs

- **Threat Detection Engine** that identifies:
  - SQL injection attempts
  - Cross-Site Scripting (XSS)
  - Path traversal attacks
  - SSH brute force attempts
  - Command injection
  - Admin panel probes

### 3. **Alert Management System**
- Create alerts based on detected threats
- Status tracking: New → Review → Investigating → Resolved
- Severity classification: Critical, High, Medium, Low, Info
- Bulk alert actions
- Advanced filtering by status, severity, type, IP
- Alert acknowledgment and resolution tracking

### 4. **Log Upload & Processing**
- Upload logs via file or paste text
- Automatic log parsing and analysis
- Real-time threat detection
- IP reputation tracking
- Suspicious activity flagging

### 5. **Security Metrics & Reporting**
- Daily security score calculation
- Alert statistics
- Source IP analysis
- Trend monitoring
- Export capabilities

### 6. **User Authentication & Authorization**
- Secure password hashing (bcrypt)
- Session management
- Role-based access control (Admin, Analyst, Viewer)
- Audit logging of all admin actions

## 🏗️ Technical Architecture

### Database Schema
```
Users
├── Logs (multiple sources)
├── Alerts (with severity levels)
├── Incidents (incident response)
├── Vulnerabilities (vulnerability tracking)
├── IP Reputation (malicious IP tracking)
├── Security Metrics (daily scoring)
├── Threat Patterns (detection rules)
└── Audit Log (admin actions)
```

### Tech Stack
- **Backend**: PHP 7+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Charts**: Chart.js
- **Server**: Apache (XAMPP compatible)

## 🚀 Installation

### Prerequisites
- XAMPP or Apache + PHP 7.0+
- MySQL 5.7+
- Modern web browser

### Setup Steps

1. **Clone/Place the project**:
   ```bash
   cp -r secure_web_app /path/to/xampp/htdocs/
   ```

2. **Create the database**:
   ```bash
   mysql -u root < setup/database.sql
   ```

3. **Configure database connection** in `config/db.php`:
   ```php
   $conn = new mysqli("localhost", "root", "", "secure_app", 3307);
   ```

4. **Update user passwords** (run this SQL):
   ```sql
   UPDATE users SET password_hash = '$2y$10$...' WHERE username = 'admin';
   ```

5. **Start Apache and MySQL** in XAMPP

6. **Access the dashboard**:
   ```
   http://localhost/secure_web_app/
   ```

## 📊 Usage Examples

### 1. Upload and Analyze Logs

**Text Input Method:**
```
1. Go to Admin → Upload Logs
2. Select "Apache Web Server"
3. Paste Apache access logs
4. Click "Process Logs"
```

**Sample Apache Log:**
```
192.168.1.100 - - [23/Apr/2026:14:33:22 +0000] "GET /admin.php HTTP/1.1" 200 5082
192.168.1.50 - - [23/Apr/2026:14:33:25 +0000] "GET /admin.php?id=1' OR '1'='1 HTTP/1.1" 403 1234
```

The system will:
✅ Parse each log entry
✅ Detect SQL injection attempt in 2nd line
✅ Create an alert with severity "CRITICAL"
✅ Update IP reputation for 192.168.1.50
✅ Recalculate security score

### 2. Review and Respond to Alerts

```
1. Dashboard → Alerts
2. View recent alerts
3. Click "Review" to acknowledge
4. Click "Investigate" to begin response
5. Click "Resolve" when complete
```

### 3. Monitor Security Metrics

Dashboard shows:
- **Security Score**: Based on alerts and resolution rate
- **Total Alerts**: 24-hour count
- **Severity Breakdown**: Critical/High/Medium/Low
- **Trending Charts**: Alerts over time, by source IP
- **Recent Events**: Real-time security feed

## 🔒 Security Implementation

### OWASP Top 10 Protections

1. **SQL Injection Prevention**
   - Prepared statements with parameterized queries
   - Input validation and sanitization

2. **Authentication & Session Management**
   - Bcrypt password hashing
   - Secure session handling
   - Session timeout
   - CSRF token validation (planned)

3. **XSS Prevention**
   - Output encoding
   - Content Security Policy (CSP) headers
   - Input validation

4. **Data Exposure**
   - Database encryption support
   - Secure password storage
   - Audit logging

5. **Configuration & Deployment**
   - Database connection security
   - Error handling without info disclosure
   - Secure headers

## 🎓 Skills Demonstrated

### Junior Web Developer
- ✅ Full-stack PHP development
- ✅ MySQL database design and optimization
- ✅ RESTful API design
- ✅ Responsive web design (CSS Grid, Flexbox)
- ✅ JavaScript ES6+ features
- ✅ Form handling and validation
- ✅ File upload processing
- ✅ Chart.js data visualization

### Junior SOC Analyst
- ✅ Log parsing and analysis
- ✅ Threat pattern recognition
- ✅ Alert classification and severity assessment
- ✅ Incident response workflow
- ✅ IP reputation tracking
- ✅ Security metrics and KPI monitoring
- ✅ Vulnerability assessment
- ✅ Audit logging and compliance

## 📁 Project Structure

```
secure_web_app/
├── admin/
│   ├── alerts.php              # Alert management interface
│   ├── dashboard.php           # Main SOC dashboard
│   ├── incidents.php           # Incident response tracking
│   ├── vulnerabilities.php     # Vulnerability management
│   ├── upload.php              # Log upload interface
│   └── reports.php             # Generate reports
├── api/
│   ├── alerts.php              # Fetch alerts
│   ├── alerts-filtered.php     # Filter alerts
│   ├── metrics.php             # Get security metrics
│   ├── security-feed.php       # Real-time events
│   ├── update-alert.php        # Update alert status
│   └── upload-logs.php         # Process uploaded logs
├── auth/
│   ├── login.php               # Login form
│   ├── logout.php              # Logout
│   └── register.php            # Registration
├── assets/
│   ├── css/
│   │   └── dashboard.css       # Main stylesheet
│   └── js/
│       ├── dashboard.js        # Dashboard functionality
│       └── alerts.js           # Alerts functionality
├── config/
│   └── db.php                  # Database connection
├── includes/
│   ├── auth_check.php          # Session validation
│   ├── header.php              # Page header
│   ├── footer.php              # Page footer
│   └── LogAnalyzer.php         # Threat detection engine
├── logs/
│   └── loggers.php             # Activity logging
└── setup/
    └── database.sql            # Database schema

```

## 📈 Future Enhancements

- [ ] Machine learning-based threat detection
- [ ] Email/SMS alert notifications
- [ ] Integration with external security APIs
- [ ] Advanced reporting (PDF export)
- [ ] Multi-tenancy support
- [ ] Real-time log streaming (WebSocket)
- [ ] Mobile app
- [ ] SIEM integration
- [ ] Threat intelligence feeds
- [ ] Advanced analytics

## 🔐 Compliance & Standards

- ✅ OWASP Top 10
- ✅ NIST Cybersecurity Framework basics
- ✅ Secure coding practices
- ✅ Data protection principles
- ✅ Audit trail requirements

## 🤝 Contributing

This is a portfolio project for junior roles. Suggestions and improvements welcome!

## 📝 License

Created for educational and portfolio purposes.

## 👨‍💼 About This Project

**Built by**: A fresh graduate aspiring to work as a Junior Web Developer & Junior SOC Analyst

**Why this project**:
- Shows full-stack development skills
- Demonstrates security operations knowledge
- Combines two career paths
- Real-world applicable
- Impressive portfolio piece

## 🎯 Interview Talking Points

1. **Architecture**: Explain the database design and why normalization matters
2. **Security**: Discuss threat detection patterns and why they work
3. **UX**: Talk about responsive design and user workflow
4. **APIs**: Explain RESTful principles used
5. **Scalability**: Discuss optimization opportunities (indexing, caching)
6. **Security Scoring**: Explain the algorithm and business logic
7. **Career Path**: Show how web dev and security ops can combine

## 📞 Support

For questions or issues, refer to the code comments and documentation within each file.

---

**Status**: MVP Complete ✅ | **Version**: 1.0.0 | **Last Updated**: April 2026
