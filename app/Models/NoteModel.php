<?php

class NoteModel {
    public function getLatestBySection($sectionId) {
        $sql = "SELECT * FROM notes WHERE section_id = ? ORDER BY created_at DESC LIMIT 1";
        return $this->db->query($sql, [$sectionId])->getRow();
    }
} 