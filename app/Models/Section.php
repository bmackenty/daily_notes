<?php
namespace App\Models;

/**
 * Section Model
 * 
 * This class handles all database operations related to sections in the application.
 * Sections are subdivisions of courses, representing specific class periods or groups.
 * Each section can have multiple notes associated with it.
 * 
 * Key Features:
 * - CRUD operations for sections
 * - Course-based section organization
 * - Position-based ordering
 * - Meeting time and place management
 * 
 * Database Structure:
 * - sections table: Stores section information
 * - Foreign keys: course_id
 * - Related tables: courses, notes
 */
class Section {
    /** @var \PDO Database connection instance */
    private $db;
    
    /**
     * Constructor
     * 
     * @param \PDO $db Database connection instance
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all sections for a specific course
     * 
     * Retrieves sections ordered by their position within the course
     * 
     * @param int $courseId ID of the course
     * @return array Array of sections for the specified course
     */
    public function getAllByCourse($courseId) {
        $stmt = $this->db->prepare("SELECT * FROM sections WHERE course_id = ? ORDER BY position");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get a single section by ID
     * 
     * @param int $id ID of the section to retrieve
     * @return array|false Section data if found, false otherwise
     */
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM sections WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create a new section
     * 
     * @param array $data Section data including:
     *                    - course_id: ID of the parent course
     *                    - name: Section name
     *                    - description: Section description
     *                    - position: Order within the course
     *                    - meeting_time: When the section meets
     *                    - meeting_place: Where the section meets
     * @return bool True if creation was successful, false otherwise
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO sections (
                course_id, name, description, position, meeting_time, meeting_place
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['course_id'],
            $data['name'],
            $data['description'],
            $data['position'],
            $data['meeting_time'],
            $data['meeting_place']
        ]);
    }
    
    /**
     * Update an existing section
     * 
     * @param int $id ID of the section to update
     * @param array $data Updated section data including:
     *                    - name: Section name
     *                    - description: Section description
     *                    - position: Order within the course
     *                    - meeting_time: When the section meets
     *                    - meeting_place: Where the section meets
     * @return bool True if update was successful, false otherwise
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE sections 
            SET name = ?, description = ?, position = ?, meeting_time = ?, meeting_place = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['position'],
            $data['meeting_time'],
            $data['meeting_place'],
            $id
        ]);
    }
    
    /**
     * Delete a section
     * 
     * Note: This will fail if the section has associated notes
     * due to foreign key constraints
     * 
     * @param int $id ID of the section to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM sections WHERE id = ?");
        return $stmt->execute([$id]);
    }
} 