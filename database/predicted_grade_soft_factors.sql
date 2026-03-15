-- Add config keys for soft factors (run if you already have predicted_grade_config)
INSERT INTO predicted_grade_config (config_key, config_value) VALUES
('weight_study_habits', '0'),
('weight_independent_coding', '0')
ON DUPLICATE KEY UPDATE config_key = config_key;
