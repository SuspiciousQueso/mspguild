CREATE TABLE IF NOT EXISTS tickets (
                                       id INT AUTO_INCREMENT PRIMARY KEY,
                                       user_id INT NOT NULL,
                                       subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open', 'in-progress', 'waiting-on-client', 'closed') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high', 'emergency') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
    );

CREATE TABLE IF NOT EXISTS ticket_comments (
                                               id INT AUTO_INCREMENT PRIMARY KEY,
                                               ticket_id INT NOT NULL,
                                               user_id INT NOT NULL,
                                               comment TEXT NOT NULL,
                                               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                               FOREIGN KEY (ticket_id) REFERENCES tickets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
    );