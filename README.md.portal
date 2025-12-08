# MSP Customer Portal

A modern, full-featured customer portal and business hub for Managed Service Providers (MSPs).

## Features

- **Professional Homepage** - Modern landing page with service overview and call-to-action
- **Secure Authentication** - Password hashing (bcrypt), session management, CSRF protection
- **Customer Dashboard** - Protected area with user information and quick links
- **Contact Form** - Standalone contact page with database storage
- **Responsive Design** - Bootstrap 5 mobile-first design
- **Modular Architecture** - Easy to extend and customize
- **Future-Ready** - Designed for CRM/ticketing system integration

## Technology Stack

- **Backend**: PHP 8+ with PDO
- **Frontend**: HTML5, Bootstrap 5, JavaScript
- **Database**: MySQL/MariaDB
- **Security**: Prepared statements, CSRF tokens, XSS protection, secure sessions

## Project Structure

```
msp-portal/
├── config/
│   ├── database.php          # Database connection
│   └── config.php             # Application settings
├── public/
│   ├── index.php              # Homepage
│   ├── login.php              # Login portal
│   ├── dashboard.php          # Customer dashboard
│   ├── contact.php            # Contact form
│   ├── logout.php             # Logout handler
│   ├── css/custom.css         # Custom styles
│   └── js/main.js             # Custom JavaScript
├── includes/
│   ├── header.php             # Reusable header
│   ├── footer.php             # Reusable footer
│   └── auth.php               # Authentication functions
├── api/
│   ├── login_handler.php      # Login processing
│   ├── contact_handler.php    # Contact form processing
│   └── register_handler.php   # User registration
├── assets/
│   └── images/                # Logos, icons, images
├── sql/
│   └── schema.sql             # Database schema
└── README.md                  # This file
```

## Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache, Nginx, or PHP built-in server)
- Windows: XAMPP, WAMP, or Laragon recommended

## Installation & Setup

### Step 1: Install a Local Web Server

**For Windows (recommended: XAMPP)**

1. Download XAMPP from: https://www.apachefriends.org/
2. Install XAMPP to `C:\xampp`
3. Start Apache and MySQL from XAMPP Control Panel

**Alternative: PHP Built-in Server (for development only)**
- Requires PHP 8+ installed on your system
- See "Quick Start" section below

### Step 2: Move Project Files

Copy the `msp-portal` folder to your web server's root directory:

- **XAMPP**: `C:\xampp\htdocs\msp-portal`
- **WAMP**: `C:\wamp64\www\msp-portal`
- **Laragon**: `C:\laragon\www\msp-portal`

### Step 3: Create the Database

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "Import" tab
3. Choose file: `msp-portal/sql/schema.sql`
4. Click "Go" to import

**Or use command line:**

```bash
# Navigate to the project directory
cd C:\Users\bbaldwin\msp-portal

# Import the database schema
mysql -u root -p < sql\schema.sql
```

### Step 4: Configure Database Connection

Edit `config/database.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'msp_portal');
define('DB_USER', 'root');          // Your MySQL username
define('DB_PASS', '');              // Your MySQL password
```

### Step 5: Update Site Configuration

Edit `config/config.php`:

```php
define('SITE_NAME', 'Your MSP Company');
define('SITE_URL', 'http://localhost/msp-portal/public');
define('SUPPORT_EMAIL', 'support@yourmsp.com');
define('SUPPORT_PHONE', '(555) 123-4567');
```

### Step 6: Set Permissions (if needed)

Ensure the web server can read all files:

```powershell
# If using PHP built-in server, no additional permissions needed
# For production, ensure proper file permissions
```

### Step 7: Access Your Portal

Open your browser and navigate to:

- **Homepage**: http://localhost/msp-portal/public/index.php
- **Login**: http://localhost/msp-portal/public/login.php
- **Contact**: http://localhost/msp-portal/public/contact.php

## Quick Start (PHP Built-in Server)

If you have PHP 8+ installed, you can run without XAMPP:

```powershell
# Navigate to the public directory
cd C:\Users\bbaldwin\msp-portal\public

# Start PHP built-in server
php -S localhost:8000

# Open browser to http://localhost:8000
```

**Note:** You'll still need MySQL running for database access.

## Demo Account

A demo user account is included for testing:

- **Email**: demo@example.com
- **Password**: Demo123!

**IMPORTANT**: Remove this demo account before deploying to production!

## Database Schema

### Users Table
Stores customer account information:
- id, email, password_hash, full_name
- company_name, contact_phone, service_tier
- is_active, created_at, last_login

### Contact Submissions Table
Stores contact form submissions:
- id, name, email, company, phone
- message, status, submitted_at

### User Sessions Table
For enhanced session management (future use):
- id, user_id, session_token
- ip_address, user_agent
- created_at, expires_at

## Customization

### Update Branding

1. **Logo**: Add your logo to `assets/images/`
2. **Colors**: Edit CSS variables in `public/css/custom.css`
3. **Company Info**: Update constants in `config/config.php`

### Add New Pages

1. Create new PHP file in `public/`
2. Include header: `require_once __DIR__ . '/../includes/header.php';`
3. Add your content
4. Include footer: `require_once __DIR__ . '/../includes/footer.php';`
5. Update navigation in `includes/header.php`

### Add Resume/Documents

Place your resume PDF in `assets/` and update the URL in `config/config.php`:

```php
define('RESUME_URL', '/msp-portal/assets/resume.pdf');
```

## Security Best Practices

### For Development
- Keep demo account for testing
- Error reporting is enabled
- HTTPS not required

### For Production

1. **Remove demo user** from database:
   ```sql
   DELETE FROM users WHERE email = 'demo@example.com';
   ```

2. **Update config.php**:
   - Set strong database password
   - Change `SITE_URL` to your domain
   - Enable HTTPS
   - Set `session.cookie_secure` to 1

3. **Disable error display**:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

4. **Use environment variables** for sensitive data

5. **Regular updates**: Keep PHP, MySQL, and dependencies updated

## Future Integrations

### CRM/Ticketing System

The portal is designed for easy integration:

1. **Database Integration**: The `contact_submissions` table includes a `status` field for tracking
2. **API Endpoints**: Add webhook handlers in `api/` directory
3. **User Roles**: Add `role` field to users table for admin/customer separation

Example CRM integrations:
- HubSpot API
- Zendesk API  
- FreshDesk API
- Custom ticketing system

### Adding User Roles

```sql
ALTER TABLE users ADD COLUMN role ENUM('customer', 'admin', 'technician') DEFAULT 'customer';
```

Update `includes/auth.php` to add authorization checks.

## Troubleshooting

### "Database connection failed"
- Check MySQL is running
- Verify credentials in `config/database.php`
- Ensure database `msp_portal` exists

### "Page not found" / 404 errors
- Check `SITE_URL` in `config/config.php` matches your setup
- Ensure files are in correct directory
- Verify web server is running

### CSS/JS not loading
- Check `SITE_URL` path is correct
- Clear browser cache
- Check browser console for errors

### Session issues
- Ensure PHP session directory is writable
- Check `session.save_path` in php.ini

## Development Roadmap

Potential enhancements:
- [ ] Password reset functionality
- [ ] Email verification for new users
- [ ] User profile editing
- [ ] File upload for support tickets
- [ ] Live chat integration
- [ ] Knowledge base/FAQ system
- [ ] Invoice/billing portal
- [ ] Admin dashboard
- [ ] Multi-factor authentication
- [ ] API documentation

## Support

For issues or questions:
- Check the troubleshooting section
- Review PHP error logs
- Contact your web hosting support

## License

This project is provided as-is for your MSP business use.

## Credits

Built with:
- Bootstrap 5 (https://getbootstrap.com/)
- Bootstrap Icons (https://icons.getbootstrap.com/)
- PHP (https://www.php.net/)
- MySQL (https://www.mysql.com/)

---

**Created for modern MSP businesses** | Professional, secure, and easy to extend
