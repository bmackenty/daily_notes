-- Add missing columns to courses table for full functionality
-- These columns are used in the form but may not exist in older database schemas

-- Add weekly_plan column
ALTER TABLE courses ADD COLUMN weekly_plan TEXT AFTER required_materials;

-- Add other missing columns that are used in the form
ALTER TABLE courses ADD COLUMN short_name VARCHAR(255) AFTER name;
ALTER TABLE courses ADD COLUMN aims TEXT AFTER description;
ALTER TABLE courses ADD COLUMN assessment TEXT AFTER aims;
ALTER TABLE courses ADD COLUMN policies TEXT AFTER rules;
ALTER TABLE courses ADD COLUMN academic_integrity TEXT AFTER policies;
ALTER TABLE courses ADD COLUMN prerequisites TEXT AFTER academic_integrity;
ALTER TABLE courses ADD COLUMN google_classroom_link VARCHAR(255) AFTER library_link;
ALTER TABLE courses ADD COLUMN default_tags TEXT AFTER google_classroom_link; 