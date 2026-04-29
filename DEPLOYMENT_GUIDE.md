# 🌐 Deploy to a Web Server - Complete Guide

## Deployment Options

Choose one based on your needs and budget:

### Option 1: Heroku (Recommended for Beginners)
- Free tier available
- Easy deployment
- Good for portfolios

### Option 2: DigitalOcean
- $4-5/month
- Full control
- Professional hosting

### Option 3: Bluehost/SiteGround
- Shared hosting ($2-10/month)
- Includes email
- Good for production

### Option 4: AWS/Azure/Google Cloud
- Pay-as-you-go
- Highly scalable
- Most professional

---

## Deployment Option 1: DigitalOcean (Recommended)

### Step 1: Create DigitalOcean Account
1. Go to https://www.digitalocean.com
2. Sign up (you get $200 credit for new users)
3. Create a new Droplet

### Step 2: Create a Droplet
1. Click "Create" → "Droplets"
2. **Image**: Ubuntu 20.04 LTS
3. **Plan**: Basic ($4/month)
4. **Region**: Choose closest to you
5. Click "Create Droplet"

### Step 3: Connect via SSH
```bash
ssh root@YOUR_DROPLET_IP
```

### Step 4: Install Software Stack
```bash
# Update system
apt update && apt upgrade -y

# Install Apache
apt install -y apache2

# Install PHP
apt install -y php php-mysqli php-json php-curl

# Install MySQL
apt install -y mysql-server

# Enable Apache modules
a2enmod rewrite
a2enmod php7.4
systemctl restart apache2
```

### Step 5: Upload Project Files
```bash
# On your local machine
scp -r C:\xampp\htdocs\secure_web_app root@YOUR_DROPLET_IP:/var/www/html/secure_web_app

# Or use SFTP client (FileZilla)
```

### Step 6: Set Permissions
```bash
# SSH into droplet
ssh root@YOUR_DROPLET_IP

# Set permissions
chmod -R 755 /var/www/html/secure_web_app
chown -R www-data:www-data /var/www/html/secure_web_app
```

### Step 7: Configure Apache Virtual Host
```bash
nano /etc/apache2/sites-available/secure_web_app.conf
```

Add this content:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/html/secure_web_app

    <Directory /var/www/html/secure_web_app>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/secure_web_app_error.log
    CustomLog ${APACHE_LOG_DIR}/secure_web_app_access.log combined
</VirtualHost>
```

Save (Ctrl+O, Enter, Ctrl+X)

### Step 8: Enable Site
```bash
a2ensite secure_web_app.conf
a2dissite 000-default.conf
systemctl restart apache2
```

### Step 9: Setup Database
```bash
# Connect to MySQL
mysql -u root -p

# Create database and user
CREATE DATABASE secure_app;
CREATE USER 'app_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON secure_app.* TO 'app_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Import schema
mysql -u app_user -p secure_app < /var/www/html/secure_web_app/setup/database.sql
```

### Step 10: Update config/db.php
```bash
nano /var/www/html/secure_web_app/config/db.php
```

Change to:
```php
<?php
$conn = new mysqli("localhost", "app_user", "strong_password_here", "secure_app", 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### Step 11: Setup Domain (Optional)
1. Go to domain registrar
2. Point DNS to DigitalOcean nameservers
3. Add DNS records in DigitalOcean

### Step 12: Enable HTTPS (SSL Certificate)
```bash
# Install Certbot
apt install -y certbot python3-certbot-apache

# Get SSL certificate
certbot --apache -d your-domain.com -d www.your-domain.com

# Auto-renew
systemctl enable certbot.timer
```

### Step 13: Access Your App
Visit: https://your-domain.com/secure_web_app/

---

## Deployment Option 2: Heroku

### Step 1: Install Heroku CLI
```bash
# Download from https://devcenter.heroku.com/articles/heroku-cli
```

### Step 2: Create Heroku Account
```bash
heroku login
```

### Step 3: Create Procfile
Create file: `Procfile` (no extension)
```
web: vendor/bin/heroku-php-apache2 public/
```

### Step 4: Create Composer.json
```json
{
    "require": {
        "php": "^7.4"
    }
}
```

### Step 5: Deploy
```bash
cd C:\xampp\htdocs\secure_web_app
git init
git add .
git commit -m "Initial deployment"

heroku create your-app-name
git push heroku main
```

### Step 6: Setup Database
```bash
heroku addons:create cleardb:ignite
```

---

## Database Backup Strategy

### Automated Backups
```bash
# Create backup script
nano /usr/local/bin/backup-db.sh
```

Add:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u app_user -p'password' secure_app > /backups/secure_app_$DATE.sql
```

### Cron Job for Daily Backups
```bash
# Edit crontab
crontab -e

# Add this line (daily at 2 AM)
0 2 * * * /usr/local/bin/backup-db.sh
```

---

## Monitoring & Maintenance

### Check Server Status
```bash
# CPU/Memory usage
top

# Disk usage
df -h

# Apache status
systemctl status apache2

# MySQL status
systemctl status mysql
```

### View Logs
```bash
# Apache access log
tail -f /var/log/apache2/access.log

# Apache error log
tail -f /var/log/apache2/error.log

# Application logs
tail -f /var/www/html/secure_web_app/logs/*.log
```

---

## Security Hardening

### 1. Update Permissions
```bash
# Restrict config file
chmod 600 /var/www/html/secure_web_app/config/db.php

# Make logs directory writable only by web server
chmod 750 /var/www/html/secure_web_app/logs
```

### 2. Disable Directory Listing
```bash
# In /etc/apache2/apache2.conf
<Directory /var/www/html>
    Options -Indexes
</Directory>
```

### 3. Set Security Headers
```apache
# In .htaccess
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
```

### 4. Enable ModSecurity
```bash
apt install -y libapache2-mod-security2
a2enmod security2
systemctl restart apache2
```

---

## Performance Optimization

### Enable Caching
```php
// In config file
header('Cache-Control: max-age=3600');
```

### Enable Gzip Compression
```bash
a2enmod deflate
systemctl restart apache2
```

### Database Query Optimization
```bash
# Add indexes
mysql -u app_user -p secure_app < indexes.sql
```

---

## Cost Breakdown

| Provider | Tier | Cost | Best For |
|----------|------|------|----------|
| Heroku | Free/Hobby | Free/$7/mo | Beginners, quick deploy |
| DigitalOcean | Basic | $4/month | Reliable, full control |
| Bluehost | Starter | $2.95/mo | Simple, good support |
| AWS | Free Tier | Free (1 yr) | Scalable, professional |

---

## Post-Deployment Checklist

- [ ] Domain name registered and pointing to server
- [ ] SSL certificate installed and auto-renewing
- [ ] Database backup strategy implemented
- [ ] Email notifications configured
- [ ] Monitoring/alerts setup
- [ ] Regular security updates scheduled
- [ ] Analytics/logging enabled
- [ ] Uptime monitoring configured
- [ ] Admin user credentials secured
- [ ] .env file excluded from git

---

## Troubleshooting

### Database Connection Issues
```bash
# Check MySQL is running
systemctl status mysql

# Check connection from command line
mysql -u app_user -p secure_app
```

### File Permission Issues
```bash
# Fix ownership
chown -R www-data:www-data /var/www/html/secure_web_app

# Fix permissions
chmod -R 755 /var/www/html/secure_web_app
chmod 600 /var/www/html/secure_web_app/config/db.php
```

### Apache Module Issues
```bash
# Check enabled modules
apache2ctl -M

# Enable rewrite module
a2enmod rewrite
systemctl restart apache2
```

---

**Your app will be live and accessible to everyone!** 🚀
