````markdown
# ⚙️ SETUP.md: MSPGuild Installation Guide

This guide details how to set up **MSPGuild** for both local development and production deployment using **Docker containers**, eliminating dependency on specific operating systems like Windows or Linux desktop installs.

---

## 📋 Requirements

Ensure the following are installed on your target machine (local dev or VPS):

* **Git**
* **Docker Engine** & **Docker Compose** (Latest stable version)
* **SSH Client** (for VPS Deployment only)

---

## 🚀 I. Local Development Setup (Quick Start)

This process sets up a running instance of MSPGuild using Docker Compose for rapid, isolated development.

### Step 1: Clone and Enter the Project

```bash
git clone [https://github.com/SuspiciousQueso/mspguild.git](https://github.com/SuspiciousQueso/mspguild.git)
cd mspguild
````

### Step 2: Configure Environment Variables

1.  Copy the example environment file:

    ```bash
    cp .env.example .env
    ```

2.  **Action:** Open the new **`.env`** file and set your local credentials. For development, you can use simple passwords.

    ```bash
    # Example .env settings for local development
    DB_HOST=mysql_container   # Use the service name from your docker-compose file
    DB_NAME=msp_portal
    DB_USER=root
    DB_PASS=localpassword
    ```

### Step 3: Launch the Container Stack

This command builds the PHP application image, downloads the MySQL image, and starts both services as defined in `docker-compose.yaml`.

```bash
docker-compose up -d --build
```

> Wait a moment for the build process to finish and for the MySQL container to initialize.

### Step 4: Create the Database Schema

Instead of manually importing via phpMyAdmin, you will execute the SQL script *inside* the running database container.

```bash
# Get the name of your running MySQL container (e.g., mspguild_mysql_1)
CONTAINER_NAME=$(docker-compose ps -q db) 

# Execute the schema.sql file against the database
docker exec -i $CONTAINER_NAME mysql -u root -plocalpassword msp_portal < sql/schema.sql
```

> **Note:** The `-plocalpassword` flag uses the temporary password defined in your `.env` for the initial connection.

### Step 5: Update Site Configuration

Edit **`config/config.php`**:

```php
// Ensure this points to the external port you mapped in docker-compose (e.g., 8080)
define('SITE_URL', 'http://localhost:8080/public'); 
define('SITE_NAME', 'MSPGuild Portal');
// ... other site settings
```

### Step 6: Access Your Portal

The portal is now accessible via the host machine's port:

  * **Homepage:** `http://localhost:8080/public/index.php`

-----

## ☁️ II. Production VPS Deployment

This workflow is for deploying on your remote Ubuntu VPS.

1.  **Preparation:** SSH into your VPS and ensure Git and Docker are installed.
2.  **Clone & Configure:** Repeat **Steps 1 & 2** from the Local Setup, but ensure your **`.env`** file uses **strong, production passwords** and the final public domain name for `SITE_URL`.
3.  **Launch Containers:**
    ```bash
    docker-compose up -d --build
    ```
4.  **Database Creation:** Execute **Step 4** (Create Schema) on the VPS.
5.  **Reverse Proxy Setup (CRITICAL)**:
      * **Action:** Configure your VPS's primary web server (**Apache/Nginx**) to act as a **reverse proxy**. It must handle HTTPS traffic (port 443) and forward it internally to the Docker container's exposed port (e.g., 8080).
      * **Remember to restart your web server** (`sudo systemctl restart apache2` or `nginx`) after updating your config files (`mspguild.conf` and `mspguild-le-ssl.conf`).

-----

## 🧹 III. Security Best Practices (Revised for Containers)

### For Production

  * **Remove demo user** from database.
  * **Update config.php**: Set the production `SITE_URL` and ensure `session.cookie_secure` is set to `1`.
  * **Disable error display**:
    ```php
    // In config.php or environment handler:
    error_reporting(0);
    ini_set('display_errors', 0);
    ```
  * **Container Security**: Use **Docker Secrets** or proper environment handling for sensitive data instead of passing secrets directly in `docker-compose.yaml`.
  * **Regular updates**: Keep your Docker base images and system packages updated.

-----

*(All other original sections like Database Schema, Customization, Future Integrations, and Troubleshooting should be placed below this section.)*

```
```

