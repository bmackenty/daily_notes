<?php
namespace App\Models;

/**
 * Course Model
 * 
 * This class handles all database operations related to courses in the application.
 * Courses are the top-level organizational unit, containing multiple sections.
 * 
 * Key Features:
 * - CRUD operations for courses
 * - Course lookup by code
 * - Teacher profile associations
 * - Section management (through relationships)
 * 
 * Database Structure:
 * - courses table: Stores course information
 * - Foreign keys: teacher_profile_id (optional)
 * - Related tables: sections, teacher_profiles
 */
class Course {
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
     * Get all courses
     * 
     * Retrieves all courses from the database, ordered by name
     * 
     * @return array Array of all courses
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM courses ORDER BY name");
        return $stmt->fetchAll();
    }
    
    /**
     * Get a single course by ID
     * 
     * @param int $id ID of the course to retrieve
     * @return array|false Course data if found, false otherwise
     */
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create a new course
     * 
     * @param array $data Course data including:
     *                    - name: Course name
     *                    - description: Course description (optional)
     *                    - code: Course code
     * @return int|false ID of the newly created course, or false on failure
     */
    public function create($data) {
        $sql = "INSERT INTO courses (name, description, code) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['code']
        ]);
        return $this->db->lastInsertId();
    }
    
    /**
     * Find a course by its code
     * 
     * @param string $code Course code to search for
     * @return array|false Course data if found, false otherwise
     */
    public function findByCode($code) {
        $sql = "SELECT * FROM courses WHERE code = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Update an existing course
     * 
     * @param int $id ID of the course to update
     * @param array $data Updated course data including:
     *                    - name: Course name
     *                    - description: Course description (optional)
     * @return bool True if update was successful, false otherwise
     */
    public function update($id, $data) {
        $sql = "UPDATE courses SET name = ?, description = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $id
        ]);
    }
    
    /**
     * Delete a course
     * 
     * Note: This will fail if the course has associated sections
     * due to foreign key constraints
     * 
     * @param int $id ID of the course to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function delete($id) {
        $sql = "DELETE FROM courses WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Get a course by ID (alias for get method)
     * 
     * @param int $id ID of the course to retrieve
     * @return array|false Course data if found, false otherwise
     */
    public function getById($id) {
        $sql = "SELECT * FROM courses WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get all courses associated with a teacher profile
     * 
     * @param int $profileId ID of the teacher profile
     * @return array Array of courses associated with the teacher
     */
    public function getCoursesByTeacherProfileId($profileId) {
        $sql = "SELECT * FROM courses WHERE teacher_profile_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$profileId]);
        return $stmt->fetchAll();
    }
} 