-- MSP Customer Portal Database Schema
-- Run this file to create the database and tables

CREATE DATABASE IF NOT EXISTS mspguild;
USE mspguild;

-- Users table for customer authentication and information
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255),
    contact_phone VARCHAR(50),
    service_tier ENUM('Basic', 'Professional', 'Enterprise', 'Custom') DEFAULT 'Basic',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_company (company_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact form submissions table
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    company VARCHAR(255),
    phone VARCHAR(50),
    message TEXT NOT NULL,
    status ENUM('New', 'In Progress', 'Resolved', 'Closed') DEFAULT 'New',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_submitted (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User sessions table for enhanced session management (future use)
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (session_token),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tickets table for support requests
-- Tickets table for support requests
CREATE TABLE IF NOT EXISTS tickets (
                                       id INT AUTO_INCREMENT PRIMARY KEY,

                                       user_id INT NOT NULL,

    -- FrontDesk additions (code expects these)
                                       site_id INT NULL,
                                       ticket_type ENUM('R','I','B','Q') NOT NULL DEFAULT 'R',
    -- R=Request, I=Incident, B=Billing, Q=Question (adjust if you want)

    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,

    status ENUM('open', 'in-progress', 'waiting-on-client', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high', 'emergency') DEFAULT 'medium',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_tickets_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_user_id (user_id),
    INDEX idx_site_id (site_id),
    INDEX idx_status (status),
    INDEX idx_type (ticket_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Ticket comments for communication history
CREATE TABLE IF NOT EXISTS ticket_messages (
                                               id INT AUTO_INCREMENT PRIMARY KEY,

                                               ticket_id INT NOT NULL,
                                               user_id INT NULL,

                                               body MEDIUMTEXT NOT NULL,

                                               visibility ENUM('public', 'internal') NOT NULL DEFAULT 'public',
    is_system TINYINT(1) NOT NULL DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ticket_messages_ticket
    FOREIGN KEY (ticket_id) REFERENCES tickets(id)
    ON DELETE CASCADE,

    CONSTRAINT fk_ticket_messages_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL,

    KEY idx_ticket_created (ticket_id, created_at),
    KEY idx_visibility (visibility)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password reset tokens
-- Add this to your DB if you want forgot-password support.

CREATE TABLE IF NOT EXISTS password_resets (
                                               id INT AUTO_INCREMENT PRIMARY KEY,
                                               user_id INT NOT NULL,
                                               token_hash CHAR(64) NOT NULL,
    requested_ip VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    UNIQUE KEY uq_token_hash (token_hash),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_expires (expires_at),
    INDEX idx_used (used_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/* =========================================
   FrontDesk / MSPGuild migrations
   - Sites (tenants), queues, access
   - Tickets: site_id, queue_id, ticket_type, ticket_number
   - Users: site_id, is_admin, enabled_modules
   ========================================= */

-- ---------- SITES ----------
CREATE TABLE IF NOT EXISTS sites (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Your home site
INSERT INTO sites (id, code, name)
VALUES (1, 'GUILD', 'Home Guild')
    ON DUPLICATE KEY UPDATE code = VALUES(code), name = VALUES(name);

-- ---------- QUEUES ----------
CREATE TABLE IF NOT EXISTS queues (
      id INT AUTO_INCREMENT PRIMARY KEY,
      site_id INT NOT NULL,
      name VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_site_slug (site_id, slug),
    CONSTRAINT fk_queues_site FOREIGN KEY (site_id) REFERENCES sites(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO queues (site_id, name, slug)
VALUES (1, 'FrontDesk', 'frontdesk')
    ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ---------- USERS: add columns only if missing ----------
SET @db := DATABASE();

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'users' AND COLUMN_NAME = 'site_id'
);
SET @sql := IF(@col_exists = 0, 'ALTER TABLE users ADD COLUMN site_id INT NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'users' AND COLUMN_NAME = 'is_admin'
);
SET @sql := IF(@col_exists = 0, 'ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'users' AND COLUMN_NAME = 'enabled_modules'
);
SET @sql := IF(@col_exists = 0, 'ALTER TABLE users ADD COLUMN enabled_modules TEXT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK users.site_id only if missing
SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND TABLE_NAME = 'users' AND CONSTRAINT_NAME = 'fk_users_site'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE users ADD CONSTRAINT fk_users_site FOREIGN KEY (site_id) REFERENCES sites(id)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------- TICKETS: add columns only if missing ----------
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'tickets' AND COLUMN_NAME = 'site_id'
);
SET @sql := IF(@col_exists = 0, 'ALTER TABLE tickets ADD COLUMN site_id INT NOT NULL DEFAULT 1', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'tickets' AND COLUMN_NAME = 'queue_id'
);
SET @sql := IF(@col_exists = 0, 'ALTER TABLE tickets ADD COLUMN queue_id INT NULL', 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- NEW: ticket type (R/T)
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'tickets' AND COLUMN_NAME = 'ticket_type'
);
SET @sql := IF(@col_exists = 0, "ALTER TABLE tickets ADD COLUMN ticket_type ENUM('R','T') NOT NULL DEFAULT 'R'", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'tickets' AND COLUMN_NAME = 'ticket_number'
);
SET @sql := IF(@col_exists = 0, "ALTER TABLE tickets ADD COLUMN ticket_number VARCHAR(50) NULL", 'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK tickets.site_id only if missing
SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND TABLE_NAME = 'tickets' AND CONSTRAINT_NAME = 'fk_tickets_site'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE tickets ADD CONSTRAINT fk_tickets_site FOREIGN KEY (site_id) REFERENCES sites(id)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK tickets.queue_id only if missing
SET @fk_exists := (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND TABLE_NAME = 'tickets' AND CONSTRAINT_NAME = 'fk_tickets_queue'
);
SET @sql := IF(@fk_exists = 0,
  'ALTER TABLE tickets ADD CONSTRAINT fk_tickets_queue FOREIGN KEY (queue_id) REFERENCES queues(id)',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Assign existing tickets to default FrontDesk queue
UPDATE tickets
SET queue_id = (SELECT id FROM queues WHERE site_id = 1 AND slug='frontdesk' LIMIT 1)
WHERE queue_id IS NULL;

-- ---------- USER QUEUE ACCESS ----------
CREATE TABLE IF NOT EXISTS user_queue_access (
     user_id INT NOT NULL,
     queue_id INT NOT NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (user_id, queue_id),
    CONSTRAINT fk_uqa_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_uqa_queue FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
