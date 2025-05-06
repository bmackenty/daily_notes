<?php
namespace App\Models;

/**
 * Note Model
 * 
 * This class handles all database operations related to notes in the application.
 * Notes are the core content of the application, representing daily entries for each section.
 * 
 * Key Features:
 * - CRUD operations for notes
 * - Tag management for notes
 * - Search functionality
 * - Section-specific note retrieval
 * 
 * Database Structure:
 * - notes table: Stores the main note content
 * - note_tags table: Links notes to tags
 * - Foreign keys: user_id, section_id
 */
class Note {
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
     * Create a new note
     * 
     * This method handles the creation of a new note, including:
     * - Inserting the note into the database
     * - Creating/updating associated tags
     * - Managing database transactions
     * 
     * @param array $data Note data including:
     *                    - user_id: ID of the user creating the note
     *                    - section_id: ID of the section the note belongs to
     *                    - title: Note title (also used as tag)
     *                    - content: Note content
     *                    - date: Note date
     * @return bool True if creation was successful, false otherwise
     */
    public function create($data) {
        $this->db->beginTransaction();
        try {
            // Insert the main note record
            $stmt = $this->db->prepare("
                INSERT INTO notes (user_id, section_id, title, content, date)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['user_id'],
                $data['section_id'],
                $data['title'],
                $data['content'],
                $data['date']
            ]);
            
            $noteId = $this->db->lastInsertId();
            
            // Handle tag creation/linking
            if (!empty($data['title'])) {
                $tagModel = new Tag($this->db);
                $tagId = $tagModel->findOrCreate($data['title']);
                
                // Link the note to the tag
                $stmt = $this->db->prepare("
                    INSERT INTO note_tags (note_id, tag_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$noteId, $tagId]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all notes for a specific section
     * 
     * @param int $sectionId ID of the section
     * @return array Array of notes, ordered by date (newest first)
     */
    public function getAllBySection($sectionId) {
        $sql = "SELECT * FROM notes WHERE section_id = ? ORDER BY date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update an existing note
     * 
     * This method handles updating a note, including:
     * - Updating the note content
     * - Managing associated tags
     * - Handling database transactions
     * 
     * @param int $id ID of the note to update
     * @param array $data Updated note data
     * @return bool True if update was successful, false otherwise
     */
    public function update($id, $data) {
        $this->db->beginTransaction();
        try {
            // Update the main note record
            $sql = "UPDATE notes 
                    SET title = ?, content = ?, date = ?, 
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['date'],
                $id
            ]);

            // Update associated tags
            if (!empty($data['title'])) {
                // Remove existing tag associations
                $stmt = $this->db->prepare("DELETE FROM note_tags WHERE note_id = ?");
                $stmt->execute([$id]);
                
                // Create new tag and associate it
                $tagModel = new Tag($this->db);
                $tagId = $tagModel->findOrCreate($data['title']);
                
                $stmt = $this->db->prepare("
                    INSERT INTO note_tags (note_id, tag_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$id, $tagId]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Get a single note by ID
     * 
     * @param int $id ID of the note to retrieve
     * @return array|false Note data if found, false otherwise
     */
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get the most recent note for a section
     * 
     * @param int $sectionId ID of the section
     * @return array|false Most recent note if found, false otherwise
     */
    public function getLastBySection($sectionId) {
        $sql = "SELECT * FROM notes WHERE section_id = :section_id ORDER BY date DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['section_id' => $sectionId]);
        return $stmt->fetch();
    }

    /**
     * Search notes by content or title
     * 
     * This method performs a search across note titles and content,
     * optionally filtering by section. Results include related
     * section and course information.
     * 
     * @param string $query Search query
     * @param int|null $sectionId Optional section ID to filter results
     * @return array Array of matching notes with section and course information
     */
    public function search($query, $sectionId = null) {
        $sql = "SELECT n.*, s.name as section_name, c.name as course_name 
                FROM notes n 
                JOIN sections s ON n.section_id = s.id 
                JOIN courses c ON s.course_id = c.id 
                WHERE (n.title LIKE ? OR n.content LIKE ?)";
        
        $params = ["%$query%", "%$query%"];
        
        if ($sectionId) {
            $sql .= " AND n.section_id = ?";
            $params[] = $sectionId;
        }
        
        $sql .= " ORDER BY n.date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
} 