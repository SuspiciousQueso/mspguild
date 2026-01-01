# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

MSPGuild is a self-hosted, modular MSP (Managed Service Provider) command center built with PHP. The project aims to eliminate vendor lock-in by providing an all-in-one platform for ticketing, CRM, asset management, and invoicing. The architecture emphasizes self-sufficiency and runs entirely on Docker containers.

## Development Commands

### Docker Operations
```powershell
# Start all services (Traefik, Nginx, PHP-FPM, MariaDB, Adminer)
docker compose up -d --build

# Stop all services
docker compose down

# View running containers and logs
docker compose ps
docker compose logs -f [service-name]

# Restart a specific service
docker compose restart php
docker compose restart nginx
```

### Database Management
```powershell
# Initialize/reset database schema
$CONTAINER_NAME = docker compose ps -q db
docker exec -i $CONTAINER_NAME mysql -u root -p${env:DB_ROOT_PASSWORD} mspguild < sql/schema.sql

# Access database directly
docker compose exec db mysql -u root -p${env:DB_ROOT_PASSWORD} mspguild

# View Adminer web UI
# Access at: https://adminer.mspguild.tech (production) or http://localhost:8080 (local)
```

### PHP/Composer Operations
```powershell
# Run Composer inside the PHP container
docker compose exec php composer install
docker compose exec php composer update
docker compose exec php composer dump-autoload
```

### SSL/ACME Certificate Setup (Production)
```powershell
# Prepare Let's Encrypt storage (first-time setup on VPS)
mkdir -p ./traefik-letsencrypt
New-Item -ItemType File -Path ./traefik-letsencrypt/acme.json -Force
# On Linux/VPS: chmod 600 ./traefik-letsencrypt/acme.json
```

## Architecture Overview

### Core Architecture Pattern

MSPGuild has recently transitioned from a flat/procedural structure to a **modular, object-oriented architecture** using PSR-4 autoloading. The codebase follows a "Singleton + Static Service" pattern for core infrastructure.

### Key Architectural Components

#### 1. **Bootstrap Flow** (`includes/bootstrap.php`)
- Entry point loaded by all public pages
- Loads configuration (`config/app.php`, `config/database.php`)
- Initializes Composer autoloader or falls back to manual includes
- Starts secure sessions via `Auth` class

#### 2. **Core Services** (`src/Core/`)
- **`Database.php`**: Singleton PDO connection manager
  - Access via: `Database::getConnection()`
  - Configured with exception mode and prepared statements
  
- **`Auth.php`**: Static authentication and session management
  - Methods: `startSecureSession()`, `isLoggedIn()`, `requireAuth()`, `loginUser()`, `logoutUser()`
  - Handles CSRF token generation and verification
  - Session regeneration every 30 minutes
  
- **`Session.php`**: Additional session utilities

#### 3. **Configuration** (`config/`)
- **`app.php`**: Module toggles, site URLs, CSRF settings
  - Module flags: `ENABLE_TICKETING`, `ENABLE_CRM`, `ENABLE_RMM`, `ENABLE_INVOICING`
  - Site constants: `SITE_URL`, `SITE_NAME`, `SUPPORT_EMAIL`
  
- **`database.php`**: Database credentials (reads from environment variables)
  - Defaults to Docker service name `db` for `DB_HOST`
  - Legacy `getDbConnection()` function exists but new code should use `Database::getConnection()`

#### 4. **Public Entry Points** (`public/`)
- `index.php`: Homepage
- `login.php`, `logout.php`: Authentication
- `dashboard.php`: User dashboard (requires auth)
- `user_registration.php`, `user_profile_update.php`: User management
- `modules/ticketing/`: Modular ticketing system

#### 5. **API Handlers** (`api/`)
- `login_handler.php`: Processes login POST requests
- `register_handler.php`: Processes registration
- `contact_handler.php`: Processes contact form submissions

### Namespace Convention

All new classes use the `MSPGuild\` namespace with PSR-4 autoloading:
```php
namespace MSPGuild\Core;
// Maps to src/Core/
```

### Database Schema

Tables:
- `users`: User accounts with service tiers (Basic, Professional, Enterprise, Custom)
- `tickets`: Support ticket system
- `ticket_comments`: Ticket conversation history
- `contact_submissions`: Contact form entries
- `user_sessions`: Enhanced session tracking (future use)

Default credentials for development:
- Email: `demo@example.com`
- Password: `Demo123!`
- **Remove this user in production environments**

### Module System

Modules are feature-flagged via `config/app.php`. Each module is a subdirectory in `public/modules/`:
- `ticketing/`: Support ticket interface (currently the only active module)

When building new modules:
1. Create directory in `public/modules/[module_name]/`
2. Add feature flag in `config/app.php`
3. Check flag before rendering: `if (!ENABLE_MODULE_NAME) { redirect; }`

## Docker Stack

The application runs on a multi-container stack defined in `compose.yaml`:

1. **Traefik** (port 80/443): Reverse proxy with automatic Let's Encrypt SSL
2. **Nginx** (internal): Web server, passes PHP requests to PHP-FPM
3. **PHP-FPM**: Executes application code (`docker/php/Dockerfile`)
4. **MariaDB 11** (`db` service): Primary database
5. **Adminer**: Web-based database management UI

### Important Docker Paths
- PHP code mount: `./` → `/var/www/mspguild/`
- Public web root: `./public` → `/var/www/mspguild/public/`
- Nginx config: `docker/nginx/default.conf`

### Environment Configuration

Copy `.env.example` to `.env` and configure:
```env
DB_HOST=db                        # Docker service name
DB_NAME=mspguild
DB_USER=mspguild
DB_PASS=<strong_password>
DB_ROOT_PASSWORD=<root_password>
TRAEFIK_LETSENCRYPT_EMAIL=<your_email>
```

## Code Standards

### Security Practices
- **ALWAYS use prepared statements** for database queries (PDO supports this by default)
- **ALWAYS sanitize output** using `sanitizeOutput()` function (wraps `htmlspecialchars`)
- **ALWAYS verify CSRF tokens** on forms: `Auth::generateCsrfToken()` / `Auth::verifyCsrfToken()`
- **NEVER commit** `.env` files or expose credentials
- Session cookies are `httponly`, `secure` (in production), and `samesite=strict`

### Authentication Flow
```php
// At top of protected pages:
require_once __DIR__ . '/includes/bootstrap.php';
Auth::requireAuth();  // Redirects to login if not authenticated
$user = Auth::getCurrentUser();  // Get current user data
```

### Database Access Pattern
```php
use MSPGuild\Core\Database;

$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
```

### Legacy Code Migration
The codebase is mid-transition. Some files still use:
- `getDbConnection()` from `config/database.php` (deprecated)
- Global functions like `requireAuth()`, `getCurrentUser()` (should migrate to `Auth::` class)
- Manual includes instead of autoloader

**When refactoring**: Prefer the `MSPGuild\Core\Auth` and `MSPGuild\Core\Database` classes over legacy helpers.

## Production Deployment Notes

1. DNS must point to VPS IP before Let's Encrypt certificate generation
2. Update `SITE_URL` in `config/app.php` to production domain
3. Remove demo user from database (`DELETE FROM users WHERE email='demo@example.com'`)
4. Disable error display in production:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```
5. Ensure `session.cookie_secure = 1` in `config/app.php`
6. Use Docker Secrets or environment variables for sensitive credentials

## File Structure Reference

```
mspguild/
├── api/                    # Backend handlers for forms
├── config/                 # Configuration files (app, database)
├── docker/                 # Dockerfiles and container configs
│   ├── nginx/              # Nginx virtual host config
│   └── php/                # PHP-FPM Dockerfile
├── includes/               # Shared includes (bootstrap, functions, header/footer)
├── public/                 # Web-accessible files (entry points)
│   ├── assets/             # CSS, JS, images
│   ├── modules/            # Feature modules (ticketing, etc.)
│   └── *.php               # Public pages
├── sql/                    # Database schema and migrations
├── src/                    # PSR-4 namespaced classes
│   └── Core/               # Core service classes (Auth, Database, Session)
├── vendor/                 # Composer dependencies
├── compose.yaml            # Docker Compose stack definition
├── composer.json           # PHP dependencies and autoloader
└── .env                    # Environment configuration (DO NOT COMMIT)
```

## Common Workflows

### Adding a New Feature Module
1. Create directory: `public/modules/[feature]/`
2. Add feature flag to `config/app.php`: `const ENABLE_FEATURE = true;`
3. Create `index.php` in module directory
4. Add navigation link in `includes/header.php`
5. Gate access: Check auth + feature flag at top of module files

### Adding a New Database Table
1. Add table definition to `sql/schema.sql`
2. Re-run schema import (see Database Management commands)
3. Create corresponding model class in `src/Models/` if using OOP patterns

### Debugging Database Issues
- Check container logs: `docker compose logs db`
- Verify credentials in `.env` match `compose.yaml` environment variables
- Ensure `DB_HOST` is set to `db` (Docker service name), not `localhost`
- Use Adminer UI to inspect tables and data

### Troubleshooting SSL/Certificate Issues
- Verify DNS propagation: `dig +short mspguild.tech @8.8.8.8`
- Check Traefik logs: `docker compose logs traefik`
- Ensure `acme.json` has correct permissions (600 on Linux/VPS)
- Verify email in `.env` for Let's Encrypt notifications
