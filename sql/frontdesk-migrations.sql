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
