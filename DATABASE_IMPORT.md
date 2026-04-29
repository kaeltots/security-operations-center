# 📚 Database Import Guide

## How to Import database.sql into MySQL

Choose the method that works best for you. All three methods will import the database successfully.

---

## 🔧 Method 1: Command Line (Fastest)

### For Windows Users

**Step 1: Open Command Prompt**
- Press `Windows Key + R`
- Type `cmd` and press Enter
- A black terminal window will open

**Step 2: Navigate to MySQL**
```bash
cd "C:\xampp\mysql\bin"
```

**Step 3: Connect to MySQL**
```bash
mysql -u root -p
```
- Press Enter when asked for password (leave empty if no password set)

**Step 4: Create the Database**
```sql
CREATE DATABASE secure_app;
USE secure_app;
```

**Step 5: Import the SQL File**
```bash
source C:\xampp\htdocs\secure_web_app\setup\database.sql;
```

**Step 6: Verify Success**
```sql
SHOW TABLES;
```
You should see 12 tables listed.

**Step 7: Exit MySQL**
```sql
EXIT;
```

---

## 🔧 Method 2: phpMyAdmin (Easiest for Beginners)

### Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Click **"Start"** next to Apache
3. Click **"Start"** next to MySQL

### Step 2: Open phpMyAdmin
1. Open your web browser
2. Go to: `http://localhost/phpmyadmin`
3. You should see the phpMyAdmin interface

### Step 3: Create Database
1. Click **"New"** in the left sidebar
2. Enter database name: `secure_app`
3. Choose Collation: `utf8mb4_unicode_ci`
4. Click **"Create"**

### Step 4: Import SQL File
1. Click on the database **`secure_app`** (just created)
2. Click the **"Import"** tab at the top
3. Click **"Choose File"**
4. Navigate to: `C:\xampp\htdocs\secure_web_app\setup\database.sql`
5. Click **"Open"**
6. Click the blue **"Import"** button
7. Wait for confirmation message ✅

### Step 5: Verify
1. Click on **`secure_app`** in the left sidebar
2. You should see a list of 12 tables
3. Expand each table to see the columns

---

## 🔧 Method 3: MySQL Workbench (Most Professional)

### Step 1: Install MySQL Workbench (if not installed)
- Download from: `https://www.mysql.com/products/workbench/`
- Install and run

### Step 2: Connect to MySQL
1. Open MySQL Workbench
2. Click on the local connection (usually "Local instance MySQL...")
3. Enter password if needed (blank for XAMPP default)

### Step 3: Create Database
1. Go to **File → New Query Tab**
2. Paste this:
```sql
CREATE DATABASE secure_app;
```
3. Click the **Execute** button (lightning bolt icon)

### Step 4: Select Database
```sql
USE secure_app;
```
Click Execute

### Step 5: Import SQL File
1. Go to **File → Open SQL Script**
2. Navigate to: `C:\xampp\htdocs\secure_web_app\setup\database.sql`
3. Click **"Open"**
4. Click the **Execute** button
5. Wait for completion

### Step 6: Verify
```sql
SHOW TABLES;
```
Click Execute - should show 12 tables

---

## ⚡ Quick Reference - Copy/Paste Commands

### If using Command Line, copy these exactly:

```bash
cd "C:\xampp\mysql\bin"
mysql -u root -p
```
(Press Enter for blank password)

Then paste:
```sql
CREATE DATABASE secure_app;
USE secure_app;
source C:\xampp\htdocs\secure_web_app\setup\database.sql;
SHOW TABLES;
EXIT;
```

---

## ✅ Verification Checklist

After importing, verify with these commands in MySQL:

```sql
-- Should show "secure_app"
SHOW DATABASES;

-- Should return 12 tables
USE secure_app;
SHOW TABLES;

-- Should show columns for users table
DESCRIBE users;

-- Should show alerts table has data
DESCRIBE alerts;

-- Should show 5 severity levels
SELECT * FROM severity_levels;
```

---

## ❌ Troubleshooting

### Error: "Access Denied"
**Solution:**
- Make sure MySQL is running in XAMPP
- Try removing `-p` flag if no password set:
  ```bash
  mysql -u root
  ```

### Error: "Database already exists"
**Solution:**
- Drop the old database first:
  ```sql
  DROP DATABASE secure_app;
  CREATE DATABASE secure_app;
  ```

### Error: "Can't find file"
**Solution:**
- Make sure path is correct: `C:\xampp\htdocs\secure_web_app\setup\database.sql`
- Forward slashes in MySQL: `C:/xampp/htdocs/secure_web_app/setup/database.sql`

### Error: "Syntax error in SQL"
**Solution:**
- Check file is not corrupted
- Try importing via phpMyAdmin instead (easier)

### Tables created but no data
**Solution:**
- This is normal - the database schema is created
- Tables will be populated when you upload logs

---

## 🎯 After Import: What's Created?

The import creates:

**12 Tables:**
1. ✅ users
2. ✅ severity_levels
3. ✅ log_sources
4. ✅ logs
5. ✅ alerts
6. ✅ threat_patterns
7. ✅ vulnerabilities
8. ✅ incidents
9. ✅ incident_timeline
10. ✅ security_metrics
11. ✅ ip_reputation
12. ✅ audit_log

**Pre-populated Data:**
- 5 severity levels (CRITICAL, HIGH, MEDIUM, LOW, INFO)
- 7 log source types (Apache, System, Firewall, SSH, etc.)
- 2 test users (admin, analyst)

---

## 🚀 Next Steps After Import

1. **Verify connection in PHP:**
   - Edit `config/db.php`
   - Make sure port matches (usually 3307 for XAMPP)
   ```php
   $conn = new mysqli("localhost", "root", "", "secure_app", 3307);
   ```

2. **Start Apache & MySQL in XAMPP**

3. **Visit the app:**
   - `http://localhost/secure_web_app/`

4. **Test the app:**
   - Go to Upload Logs
   - Paste sample logs
   - Watch alerts generate! 🚨

---

## 💡 Pro Tips

**Tip 1: Backup Your Database**
```sql
mysqldump -u root secure_app > backup.sql
```

**Tip 2: Check MySQL Version**
```sql
SELECT VERSION();
```
(Should be 5.7+)

**Tip 3: Reset Database**
```sql
DROP DATABASE secure_app;
CREATE DATABASE secure_app;
source C:\xampp\htdocs\secure_web_app\setup\database.sql;
```

**Tip 4: View Database Size**
```sql
SELECT 
  table_name,
  ROUND(((data_length + index_length) / 1024 / 1024), 2) AS Size_MB
FROM information_schema.tables 
WHERE table_schema = "secure_app"
ORDER BY (data_length + index_length) DESC;
```

---

## 📞 Still Having Issues?

### Check These:

1. **Is MySQL running?**
   - Look at XAMPP Control Panel
   - MySQL should show "Running"

2. **Is the SQL file in the right place?**
   ```
   C:\xampp\htdocs\secure_web_app\setup\database.sql
   ```

3. **Are you using the right credentials?**
   - XAMPP default: username `root`, password (blank)

4. **Is the port correct?**
   - XAMPP MySQL usually runs on port 3307
   - Not 3306

---

## 🎓 Understanding the Database

After import, here's what you have:

**Users Table** - For login
- admin user (for testing)
- analyst user (for testing)

**Logs Table** - Stores all imported logs
- From Apache, System, Firewall, etc.
- Auto-populated when you upload

**Alerts Table** - Auto-generated alerts
- Created by threat detection
- Links to logs
- Has severity level

**Incidents Table** - Manual incident tracking
- You create these
- Link to alerts
- Track lifecycle

**Vulnerabilities Table** - CVE tracking
- Manual entry
- Track patches
- Remediation steps

**IP Reputation** - Suspicious IP tracking
- Auto-updated
- Score increases with alerts
- Blocks tracking

---

## ✨ Success Indicators

You'll know it worked when:

✅ Database created successfully
✅ All 12 tables appear
✅ Severity levels table has 5 rows
✅ Log sources table has 7 rows
✅ No errors in import
✅ App can connect to database
✅ Dashboard loads without errors

---

## 📋 One-Line Cheat Sheet

**Command Line Import (fastest):**
```bash
cd C:\xampp\mysql\bin && mysql -u root < C:\xampp\htdocs\secure_web_app\setup\database.sql
```

---

## 🎯 Ready to Go!

Once imported, you can:
1. Login to the app
2. Upload logs
3. See threat detection in action
4. Manage alerts
5. Track incidents
6. All with real data! 🚀

---

**Questions? Check QUICKSTART.md for next steps!**

Created: April 23, 2026
Last Updated: Today
Status: ✅ Complete
