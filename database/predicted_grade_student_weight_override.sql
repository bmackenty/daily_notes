-- Allow students to set homework & habits weight (e.g. 10%) to match their teacher's policy.
-- If set, this overrides the global config for that student.
-- 0–0.30; NULL = use global config
ALTER TABLE predicted_grade_students
ADD COLUMN weight_soft_override DECIMAL(5,4) NULL;
