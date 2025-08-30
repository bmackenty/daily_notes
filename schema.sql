-- Users table with enhanced security fields
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    failed_login_attempts INT DEFAULT 0,
    last_failed_login TIMESTAMP NULL,
    account_locked_until TIMESTAMP NULL,
    password_reset_token VARCHAR(255) NULL,
    password_reset_expires TIMESTAMP NULL,
    last_password_change TIMESTAMP NULL,
    remember_token VARCHAR(255) NULL,
    remember_token_expires TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Login attempts tracking
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(255) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    INDEX idx_ip_email (ip_address, email),
    INDEX idx_time (attempt_time)
);

-- Remember me tokens for persistent login
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token_hash (token_hash),
    INDEX idx_expires (expires_at),
    INDEX idx_user_id (user_id)
);

-- Security settings
CREATE TABLE security_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default security settings
INSERT INTO security_settings (setting_key, setting_value, description) VALUES
('max_login_attempts', '5', 'Maximum number of failed login attempts before account lockout'),
('lockout_duration', '15', 'Account lockout duration in minutes'),
('rate_limit_window', '60', 'Rate limit window in seconds'),
('max_rate_limit', '10', 'Maximum number of login attempts per rate limit window'),
('password_min_length', '8', 'Minimum password length'),
('password_require_uppercase', '1', 'Password must contain uppercase letters'),
('password_require_lowercase', '1', 'Password must contain lowercase letters'),
('password_require_numbers', '1', 'Password must contain numbers'),
('password_require_special', '1', 'Password must contain special characters');

-- Existing tables
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    date DATE NOT NULL,
    academic_year_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE SET NULL,
    INDEX idx_notes_academic_year (academic_year_id)
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE note_tags (
    note_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (note_id, tag_id),
    FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    short_name VARCHAR(255),
    description TEXT,
    aims TEXT,
    assessment TEXT,
    teacher VARCHAR(255),
    teacher_profile_id INT,
    rules TEXT,
    communication TEXT,
    required_materials TEXT,
    weekly_plan TEXT,
    policies TEXT,
    academic_integrity TEXT,
    prerequisites TEXT,
    github_link VARCHAR(255),
    lms_link VARCHAR(255),
    help_link VARCHAR(255),
    library_link VARCHAR(255),
    google_classroom_link VARCHAR(255),
    default_tags TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_profile_id) REFERENCES teacher_profiles(id)
);

CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    meeting_time VARCHAR(255),
    meeting_place VARCHAR(255),
    position INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS learning_statement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255),
    learning_statement TEXT NOT NULL,
    position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE teacher_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    full_name VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    email VARCHAR(255),
    office_hours TEXT,
    biography TEXT,
    education TEXT,
    profile_picture VARCHAR(255),
    contact_preferences TEXT,
    expertise TEXT,
    teaching_philosophy TEXT,
    personal_interests TEXT,
    achievements TEXT,
    vision_for_students TEXT,
    fun_facts TEXT,
    social_media_links TEXT,
    github_link VARCHAR(255),
    personal_webpage VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('registration_enabled', 'true', 'Allow new user registrations'),
('max_notes_per_user', '100', 'Maximum number of notes per user'),
('maintenance_mode', 'false', 'Site maintenance mode'),
('show_delete_buttons', 'false', 'Show delete buttons for courses and sections');

-- Create academic years table
CREATE TABLE academic_years (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create academic weeks table
CREATE TABLE academic_weeks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    academic_year_id INT NOT NULL,
    week_number INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    UNIQUE KEY unique_week (academic_year_id, week_number)
);

-- Create course weekly plans table
CREATE TABLE course_weekly_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    academic_week_id INT NOT NULL,
    topic VARCHAR(255) NOT NULL,
    objectives TEXT,
    resources TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_week_id) REFERENCES academic_weeks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_plan (course_id, academic_week_id)
);

ALTER TABLE notes ADD COLUMN section_id INT NOT NULL,
ADD FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE; 