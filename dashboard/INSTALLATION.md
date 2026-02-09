# Krause Global Deal Desk Dashboard - Installation Guide

## Prerequisites
- PHP 8.0 or higher
- MySQL or MariaDB
- Composer
- Write permissions on storage directories

## Installation Steps

### 1. Upload Files
Upload the entire `dashboard` folder and `storage` folder to your All-Inkl webspace:
```
/httpdocs/dashboard/
/storage/
```

### 2. Install Dependencies
SSH into your server and run:
```bash
cd /path/to/dashboard
composer install --no-dev --optimize-autoloader
```

If you don't have SSH access, run this locally and upload the `vendor` folder.

### 3. Configure Environment
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

Edit `.env` with your database credentials:
```
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password
```

### 4. Create Database
In All-Inkl control panel:
1. Create a new MySQL database
2. Note the credentials
3. Import the `database.sql` file

Or via command line:
```bash
mysql -u your_user -p your_database < database.sql
```

### 5. Set Permissions
```bash
chmod -R 755 storage/
chmod -R 755 dashboard/public/assets/
```

### 6. Test Installation
Visit: `https://krause-global.com/dashboard`

Default login:
- Username: `admin`
- Password: `ChangeMe2026!`

**IMPORTANT:** Change the password immediately after first login!

## Security Checklist
- [ ] Change admin password
- [ ] Verify `.env` is not accessible via web
- [ ] Confirm storage directory is outside webroot or protected
- [ ] Enable HTTPS
- [ ] Set SESSION_LIFETIME appropriately
- [ ] Review file upload limits

## Troubleshooting

### "Database connection failed"
- Check DB credentials in `.env`
- Verify database exists
- Check if MySQL is running

### "Permission denied" errors
- Check storage directory permissions
- Verify web server can write to storage/

### "404 Not Found"
- Check `.htaccess` is present
- Verify mod_rewrite is enabled
- Check file paths in config.php

### PDF extraction not working
- Check if pdftotext is available: `which pdftotext`
- If not, the system will use PHP parser automatically

## Directory Structure
```
/httpdocs/
├── dashboard/
│   ├── public/          # Web-accessible
│   │   ├── index.php    # Entry point
│   │   └── assets/      # CSS, JS
│   ├── app/             # Application code
│   ├── config/          # Configuration
│   ├── vendor/          # Composer dependencies
│   ├── .env             # Environment config
│   └── database.sql     # Database schema
└── storage/             # File storage (NOT web-accessible)
    ├── uploads/
    ├── extracted/
    ├── exports/
    ├── sessions/
    └── logs/
```

## Support
For issues, check logs in `storage/logs/` or contact support.
