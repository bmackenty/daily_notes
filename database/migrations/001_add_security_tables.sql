-- Add security fields to users table
ALTER TABLE users 
ADD COLUMN failed_login_attempts INT DEFAULT 0,
ADD COLUMN last_failed_login TIMESTAMP NULL,
ADD COLUMN account_locked_until TIMESTAMP NULL,
ADD COLUMN password_reset_token VARCHAR(255) NULL,
ADD COLUMN password_reset_expires TIMESTAMP NULL,
ADD COLUMN last_password_change TIMESTAMP NULL;

-- Create login attempts table
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(255) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    INDEX idx_ip_email (ip_address, email),
    INDEX idx_time (attempt_time)
);

-- Create security settings table
CREATE TABLE IF NOT EXISTS security_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default security settings if they don't exist
INSERT IGNORE INTO security_settings (setting_key, setting_value, description) VALUES
('max_login_attempts', '5', 'Maximum number of failed login attempts before account lockout'),
('lockout_duration', '15', 'Account lockout duration in minutes'),
('rate_limit_window', '60', 'Rate limit window in seconds'),
('max_rate_limit', '10', 'Maximum number of login attempts per rate limit window'),
('password_min_length', '8', 'Minimum password length'),
('password_require_uppercase', '1', 'Password must contain uppercase letters'),
('password_require_lowercase', '1', 'Password must contain lowercase letters'),
('password_require_numbers', '1', 'Password must contain numbers'),
('password_require_special', '1', 'Password must contain special characters'); 