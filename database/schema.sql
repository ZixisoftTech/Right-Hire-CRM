-- Create Database
CREATE DATABASE IF NOT EXISTS right_hire_crm;
USE right_hire_crm;

-- 1. super_admins
CREATE TABLE IF NOT EXISTS super_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login_at TIMESTAMP NULL
);

-- 2. email_otps
CREATE TABLE IF NOT EXISTS email_otps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    otp_code VARCHAR(255) NOT NULL, -- Stored hashed
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    attempt_count INT DEFAULT 0,
    resend_count INT DEFAULT 0,
    is_used TINYINT(1) DEFAULT 0,
    INDEX (email)
);

-- 3. password_resets
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255) NOT NULL,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    is_used TINYINT(1) DEFAULT 0,
    INDEX (email),
    INDEX (reset_token)
);

-- 4. user_sessions
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    device_info TEXT,
    ip_address VARCHAR(45),
    last_activity_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES super_admins(id) ON DELETE CASCADE
);

-- 5. login_activities
CREATE TABLE IF NOT EXISTS login_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    email VARCHAR(255),
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL,
    ip_address VARCHAR(45),
    device_info TEXT,
    browser VARCHAR(255),
    login_status ENUM('success', 'failed_password', 'failed_otp', 'otp_resend', 'logout', 'session_expired') NOT NULL,
    failure_reason VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES super_admins(id) ON DELETE SET NULL
);

-- Insert default super admin for testing (password is 'Admin@123')
INSERT INTO super_admins (email, password_hash, status)
VALUES ('admin@righthirecrm.com', '$2y$10$gWHjXZcobxqC7QawvwXd2Oh4FDD5fd1YzqZhWH8vd9pcnljTO2CcC', 'active')
ON DUPLICATE KEY UPDATE email=email;

-- Insert additional test super admin
INSERT INTO super_admins (email, password_hash, status)
VALUES ('Chetanprajapat007@gmail.com', '$2y$10$gWHjXZcobxqC7QawvwXd2Oh4FDD5fd1YzqZhWH8vd9pcnljTO2CcC', 'active')
ON DUPLICATE KEY UPDATE email=email;
