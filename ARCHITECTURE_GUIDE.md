# 🏗️ Architecture & Design Decisions - Deep Dive

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Database Design](#database-design)
3. [Backend Architecture](#backend-architecture)
4. [Frontend Architecture](#frontend-architecture)
5. [Security Considerations](#security-considerations)
6. [Performance Optimization](#performance-optimization)
7. [Scalability Strategy](#scalability-strategy)

---

## System Architecture

### High-Level Overview

```
┌─────────────────────────────────────────────────────────┐
│                     Browser/Client                       │
│                    (HTML5/CSS3/JS)                       │
└────────────────────────┬────────────────────────────────┘
                         │ HTTP/HTTPS
                         ▼
┌─────────────────────────────────────────────────────────┐
│              Apache Web Server (PHP 7.4+)               │
│                  RewriteEngine ON                        │
└────────┬──────────────────────────┬────────────────────┘
         │                          │
         ▼                          ▼
┌──────────────────────┐    ┌──────────────────────┐
│   admin/* Pages      │    │   api/* Endpoints    │
│  (Dashboard, Alerts, │    │ (JSON APIs for JS)   │
│   Incidents, etc)    │    │                      │
└────────┬─────────────┘    └──────────┬───────────┘
         │                             │
         └──────────────┬──────────────┘
                        ▼
         ┌──────────────────────────────────┐
         │   LogAnalyzer Class              │
         │   (Threat Detection Engine)      │
         │   - Pattern Matching             │
         │   - Alert Generation             │
         └──────────────┬───────────────────┘
                        │
                        ▼
         ┌──────────────────────────────────┐
         │  MySQL Database (12 Tables)      │
         │  - Logs, Alerts, Incidents       │
         │  - Vulnerabilities, Users        │
         │  - IP Reputation, Metrics        │
         └──────────────────────────────────┘
```

### Why This Architecture?

**Chosen: Traditional Server-Side Rendered (SSR) MVC Pattern**

Pros:
- ✅ Simple and maintainable
- ✅ Quick development
- ✅ SEO-friendly
- ✅ Server-side session management
- ✅ Easy to secure database access

Cons:
- ⚠️ Less real-time than SPA (mitigated with auto-refresh)
- ⚠️ More server load

**Alternative Considered: Single Page Application (SPA)**
- Would use React/Vue + Node.js backend
- Better for real-time updates
- More complex setup
- Decided against for initial MVP

---

## Database Design

### ER Diagram

```
┌─────────────────┐         ┌──────────────────┐
│     users       │         │    logs          │
├─────────────────┤         ├──────────────────┤
│ id (PK)         │◄────┬───│ id (PK)          │
│ username (UQ)   │     │   │ source_id (FK)   │
│ email           │     │   │ user_id (FK)     │
│ password_hash   │     │   │ timestamp        │
│ role            │     │   │ ip_address       │
│ created_at      │     │   │ event_type       │
└─────────────────┘     │   │ status           │
                        │   │ is_suspicious    │
                        │   └──────────────────┘
                        │
┌─────────────────┐     │   ┌──────────────────┐
│     alerts      │◄────┴───│ ip_reputation    │
├─────────────────┤         ├──────────────────┤
│ id (PK)         │         │ ip_address (UQ)  │
│ log_id (FK)     │         │ reputation_score │
│ alert_type      │         │ alert_count      │
│ severity_id(FK) │◄────┐   │ is_blocklisted   │
│ source_ip       │     │   │ last_seen        │
│ status          │     │   └──────────────────┘
│ detected_at     │     │
└─────────────────┘     │
                        │
┌─────────────────┐     │
│    incidents    │◄────┤
├─────────────────┤     │
│ id (PK)         │     │
│ incident_number │     │
│ title           │     │
│ severity_id(FK) │◄────┴─────┐
│ status          │           │
│ assigned_to(FK) │           │
│ created_at      │           │
└─────────────────┘           │
                              │
                   ┌──────────────────────┐
                   │  severity_levels     │
                   ├──────────────────────┤
                   │ id (PK)              │
                   │ level_name (UQ)      │
                   │ score                │
                   │ color_code           │
                   └──────────────────────┘
```

### Design Principles Applied

#### 1. **Normalization (3NF)**
- Avoided data redundancy
- Each table has single responsibility
- Minimized data anomalies

Example:
```sql
-- ❌ BAD: Denormalized (Redundant data)
CREATE TABLE logs (
    id INT,
    log_text VARCHAR(1000),
    severity_name VARCHAR(20),  -- Repeated across many rows
    severity_color VARCHAR(20)   -- Repeated across many rows
);

-- ✅ GOOD: Normalized (Separate table)
CREATE TABLE logs (
    id INT,
    severity_id INT FOREIGN KEY
);

CREATE TABLE severity_levels (
    id INT PRIMARY KEY,
    level_name VARCHAR(20) UNIQUE,
    color_code VARCHAR(20)
);
```

#### 2. **Indexing Strategy**
```sql
-- Primary Keys (automatic indexes)
PRIMARY KEY (id)

-- Foreign Keys for JOINs
FOREIGN KEY (severity_id) REFERENCES severity_levels(id)

-- Performance indexes
CREATE INDEX idx_logs_timestamp ON logs(timestamp);
CREATE INDEX idx_alerts_severity ON alerts(severity_id);
CREATE INDEX idx_incidents_status ON incidents(status);
```

**Rationale:**
- Indexes on timestamp → Fast date range queries
- Indexes on foreign keys → Fast JOINs
- Indexes on frequently filtered columns → Improves WHERE clauses

#### 3. **Data Integrity**
```sql
-- Constraints ensure data quality
UNIQUE (username) -- No duplicate usernames
NOT NULL -- Required fields
FOREIGN KEY (...) ON DELETE CASCADE -- Maintain referential integrity
CHECK (role IN ('admin', 'analyst', 'viewer')) -- Valid values only
```

### Table Responsibilities

| Table | Purpose | Cardinality |
|-------|---------|------------|
| **users** | Authentication & authorization | 1:N with logs, alerts |
| **logs** | Raw security events | 1:N with alerts |
| **alerts** | Detected threats | N:1 with severity |
| **incidents** | Security incidents (multiple alerts) | N:1 with severity |
| **vulnerabilities** | CVE/patch tracking | N:1 with severity |
| **severity_levels** | Alert/incident severity (CRITICAL, HIGH, etc) | 1:N (shared) |
| **log_sources** | Log source types (Apache, System, etc) | 1:N with logs |
| **ip_reputation** | IP threat scoring | N:N with logs/alerts |
| **security_metrics** | Daily KPIs | 1:1 with date |
| **incident_timeline** | Incident action history | N:1 with incidents |
| **threat_patterns** | Attack pattern definitions | 1:N with alerts |
| **audit_log** | Admin action tracking | N:1 with users |

---

## Backend Architecture

### Request Processing Flow

```
1. HTTP Request (to admin/alerts.php)
           ▼
2. Session Check (includes/auth_check.php)
           ▼
3. Include Header & Common Functions
           ▼
4. Render HTML + Call APIs (via JavaScript)
           ▼
5. JavaScript makes AJAX calls to api/*.php
           ▼
6. API validates, queries database, returns JSON
           ▼
7. JavaScript renders data on page
```

### API Endpoint Design

**Pattern: RESTful conventions with JSON responses**

```php
// Example: GET /api/alerts.php?status=new&severity=CRITICAL
// Returns: JSON array of alerts

{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "sql_injection",
      "severity": "CRITICAL",
      "source_ip": "192.168.1.50",
      "detected_at": "2026-04-23 14:33:25",
      "status": "new"
    }
  ],
  "total": 1,
  "page": 1,
  "per_page": 50
}
```

### LogAnalyzer Class Architecture

```php
class LogAnalyzer {
    private $threats;  // Pattern definitions
    private $conn;     // Database connection
    
    public function loadThreatPatterns()
        // Loads 7 threat patterns with regex rules
    
    public function analyzeLog($logLine, $source)
        // Returns alerts matching this log
        // Tests each regex pattern
    
    public function parseApacheLog($line)
        // Extracts: IP, timestamp, request, status
    
    public function parseSystemLog($line)
        // Extracts: timestamp, hostname, service, message
    
    public function calculateThreatScore($alerts)
        // Sums severity weights for metrics
}
```

**Why a class?**
- ✅ Encapsulation of threat detection logic
- ✅ Reusable across multiple endpoints
- ✅ Easy to test and mock
- ✅ Organized threat patterns

### Prepared Statements for SQL Injection Prevention

```php
// ❌ VULNERABLE to SQL injection
$query = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $query);

// ✅ SECURE using prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);  // "s" = string type
$stmt->execute();
$result = $stmt->get_result();
```

**Why this matters:**
- Separates SQL code from data
- Database engine ensures values are escaped
- Cannot inject SQL commands

---

## Frontend Architecture

### Page Structure

Each admin page follows this pattern:

```html
<?php
// 1. Start session and authenticate
include('../includes/auth_check.php');
?>

<!DOCTYPE html>
<html>
<head>
    <!-- 2. Include shared CSS -->
    <link rel="stylesheet" href="/secure_web_app/assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>
    <!-- 3. Main content structure -->
    <div class="dashboard-container">
        <aside class="sidebar">
            <!-- Navigation menu -->
        </aside>
        <main class="main-content">
            <!-- Page-specific content -->
        </main>
    </div>
    
    <!-- 4. Include page-specific JavaScript -->
    <script src="/secure_web_app/assets/js/dashboard.js"></script>
</body>
</html>
```

### Responsive CSS Strategy

```css
/* Mobile-first approach */
.metrics-grid {
    display: grid;
    grid-template-columns: 1fr;  /* Mobile: 1 column */
    gap: 15px;
}

/* Tablet */
@media (min-width: 768px) {
    .metrics-grid {
        grid-template-columns: repeat(2, 1fr);  /* 2 columns */
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .metrics-grid {
        grid-template-columns: repeat(3, 1fr);  /* 3 columns */
    }
}
```

**Why CSS Grid?**
- Modern layout system
- Better than floats or table layouts
- Auto-responsive with media queries
- Better performance

### JavaScript Architecture

```javascript
// Page-specific manager class
class SecurityDashboard {
    constructor() {
        this.metrics = {};
        this.init();
    }
    
    init() {
        // Setup initial state
        this.loadMetrics();
        this.setupCharts();
        this.setupAutoRefresh();
    }
    
    async loadMetrics() {
        // Fetch from /api/metrics.php
        // Update DOM elements
    }
    
    setupCharts() {
        // Initialize Chart.js instances
    }
    
    setupAutoRefresh() {
        // Poll every 30 seconds
        setInterval(() => this.updateMetrics(), 30000);
    }
}

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    new SecurityDashboard();
});
```

**Why this pattern?**
- ✅ Encapsulation (all dashboard logic in one class)
- ✅ Separation of concerns (init, load, render)
- ✅ Easy to test individual methods
- ✅ Reusable across pages

---

## Security Considerations

### 1. SQL Injection Prevention

**Multiple Layers:**
```php
// Layer 1: Prepared statements (PRIMARY)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

// Layer 2: Input validation (SECONDARY)
if (!is_numeric($id)) {
    throw new Exception("Invalid ID format");
}

// Layer 3: Output encoding (TERTIARY)
echo htmlspecialchars($user_data, ENT_QUOTES, 'UTF-8');
```

### 2. XSS Prevention

```php
// ❌ VULNERABLE
echo $user_input;  // Could contain <script> tags

// ✅ SAFE
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// ✅ SAFE (for HTML content)
echo strip_tags($user_input, '<b><i>');  // Allow only certain tags
```

### 3. Authentication Security

```php
// Secure password hashing
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Secure password verification
if (password_verify($password, $stored_hash)) {
    // Password matches
}

// Session security
session_set_cookie_params([
    'httponly' => true,    // No JavaScript access
    'secure' => true,      // HTTPS only
    'samesite' => 'Strict' // CSRF protection
]);
```

### 4. CSRF Protection

```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Include in form
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
</form>

// Validate on submission
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token mismatch');
}
```

### 5. OWASP Top 10 Coverage

| OWASP | How Addressed |
|-------|---------------|
| **A1: Injection** | Prepared statements, input validation |
| **A2: Broken Auth** | Session management, password hashing |
| **A3: Sensitive Data** | HTTPS in production, no plaintext passwords |
| **A4: XML External Entities** | Not applicable (JSON used) |
| **A5: Broken Access Control** | Role-based checks, session verification |
| **A6: Security Misconfiguration** | Secure headers, updated frameworks |
| **A7: XSS** | htmlspecialchars(), output encoding |
| **A8: Insecure Deserialization** | JSON only, no unsafe unserialize() |
| **A9: Component Vulnerabilities** | Keep libraries updated |
| **A10: Insufficient Logging** | Audit log table, error logging |

---

## Performance Optimization

### 1. Database Optimization

**Indexes:**
```sql
-- Before: Full table scan (slow)
SELECT * FROM logs WHERE timestamp > '2026-04-23' AND severity_id = 1;

-- After: Index scan (fast)
CREATE INDEX idx_logs_timestamp_severity ON logs(timestamp, severity_id);
```

**Query Optimization:**
```php
// ❌ N+1 problem: One query for logs + N queries for severities
foreach ($logs as $log) {
    $severity = $conn->query("SELECT * FROM severity_levels WHERE id = {$log['severity_id']}");
}

// ✅ Single JOIN query
$stmt = $conn->prepare("
    SELECT l.*, s.level_name, s.color_code
    FROM logs l
    JOIN severity_levels s ON l.severity_id = s.id
    LIMIT ?
");
```

### 2. Frontend Caching

```javascript
// Cache API responses
class APICache {
    constructor(ttl = 60000) {  // 60 second TTL
        this.cache = {};
        this.ttl = ttl;
    }
    
    async fetch(url) {
        const now = Date.now();
        if (this.cache[url] && now - this.cache[url].time < this.ttl) {
            return this.cache[url].data;  // Return cached
        }
        
        const data = await fetch(url).then(r => r.json());
        this.cache[url] = { data, time: now };
        return data;
    }
}
```

### 3. Pagination

```php
// Limit results to prevent full table scan
$page = $_GET['page'] ?? 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

$stmt = $conn->prepare("
    SELECT * FROM alerts
    ORDER BY detected_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $per_page, $offset);
```

---

## Scalability Strategy

### Current Architecture (Single Server)

**Limitations:**
- All code on one server
- Single database
- Auto-refresh polling (not true real-time)
- Limited concurrent users

### Future: Microservices Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  Web Server     │    │ API Server      │    │  Workers        │
│  (Dashboard)    │    │ (REST APIs)     │    │ (Log Processing)│
└────────┬────────┘    └────────┬────────┘    └────────┬────────┘
         │                      │                      │
         └──────────────────────┼──────────────────────┘
                                │
                   ┌────────────┼────────────┐
                   │            │            │
                   ▼            ▼            ▼
              ┌─────────┐  ┌─────────┐  ┌──────────┐
              │ MySQL   │  │ Redis   │  │ Message  │
              │ (Data)  │  │ (Cache) │  │ Queue    │
              └─────────┘  └─────────┘  └──────────┘
```

### Horizontal Scaling

```
Load Balancer
    │
    ├─→ App Server 1
    ├─→ App Server 2
    ├─→ App Server 3
    │
    └─→ Shared Database
        (RDS/CloudSQL)
```

### Real-Time Updates (WebSockets)

```javascript
// Currently: Polling every 30 seconds
setInterval(() => loadMetrics(), 30000);

// Future: Real-time WebSocket
const socket = new WebSocket('wss://api.example.com/metrics');
socket.onmessage = (event) => {
    updateDashboard(JSON.parse(event.data));
};
```

---

## Deployment Architecture

### Development → Production

```
Local Development (XAMPP)
    ▼
Version Control (Git/GitHub)
    ▼
Automated Tests (CI/CD)
    ▼
Staging Environment
    ▼
Production Deployment
    ▼
Monitoring & Alerts
```

### Production Considerations

```
Reverse Proxy (Nginx)
    ↓ (Load balancing)
PHP-FPM Pool Processes
    ↓
Database Connection Pool
    ↓
MySQL Replication (Master/Slave)
```

---

## Conclusion

This architecture demonstrates:
- ✅ **Solid fundamentals** (MVC pattern, normalized DB, RESTful APIs)
- ✅ **Security awareness** (prepared statements, input validation, OWASP)
- ✅ **Performance optimization** (indexes, pagination, caching)
- ✅ **Scalability planning** (future microservices strategy)
- ✅ **Professional practices** (version control, testing, documentation)

**Interview talking point:**
> "While this is a monolithic architecture suitable for MVP, I've designed it to scale. The separation of concerns (backend logic in classes, frontend in components) makes it easy to extract microservices as traffic grows. I'm also familiar with containerization (Docker) and orchestration (Kubernetes) for enterprise deployments."
