-- ============================================================
-- IB CS Predicted Grade feature - run this to add new tables
-- ============================================================

-- Students identified by unique code (no login)
CREATE TABLE IF NOT EXISTS predicted_grade_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code)
);

-- One row per score entry: category + score + weight (for weighted avg)
-- Multiple exams per topic = multiple rows per (student_id, category)
CREATE TABLE IF NOT EXISTS predicted_grade_entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    weight DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    label VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES predicted_grade_students(id) ON DELETE CASCADE,
    INDEX idx_student_category (student_id, category)
);

-- Config: component weights and IB boundaries (admin-editable later)
CREATE TABLE IF NOT EXISTS predicted_grade_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(80) NOT NULL UNIQUE,
    config_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_config_key (config_key)
);

-- Default weights: Paper 1 40%, IA 20%, Paper 2 40% (Homework/AtL 0 for now)
INSERT INTO predicted_grade_config (config_key, config_value) VALUES
('weight_paper1', '0.40'),
('weight_ia', '0.20'),
('weight_paper2', '0.40'),
('weight_homework', '0'),
('weight_atl', '0')
ON DUPLICATE KEY UPDATE config_key = config_key;

-- IB grade boundaries: min percentage for each grade 1-7
INSERT INTO predicted_grade_config (config_key, config_value) VALUES
('boundary_1', '0'),
('boundary_2', '56'),
('boundary_3', '63'),
('boundary_4', '70'),
('boundary_5', '77'),
('boundary_6', '84'),
('boundary_7', '91')
ON DUPLICATE KEY UPDATE config_key = config_key;
