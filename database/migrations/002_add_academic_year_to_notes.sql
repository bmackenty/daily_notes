-- Migration: Add academic_year_id to notes table
-- This allows filtering notes by academic year

-- Add academic_year_id column to notes table
ALTER TABLE notes ADD COLUMN academic_year_id INT NULL;

-- Add foreign key constraint
ALTER TABLE notes ADD CONSTRAINT fk_notes_academic_year 
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE SET NULL;

-- Add index for better performance when filtering by academic year
CREATE INDEX idx_notes_academic_year ON notes(academic_year_id);

-- Update existing notes to use the currently active academic year
-- This ensures existing notes are not lost when the migration runs
UPDATE notes n 
JOIN sections s ON n.section_id = s.id 
JOIN courses c ON s.course_id = c.id 
JOIN academic_years ay ON ay.is_active = 1 
SET n.academic_year_id = ay.id 
WHERE n.academic_year_id IS NULL; 