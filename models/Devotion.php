<?php
/**
 * Devotion Model
 */

require_once __DIR__ . '/../includes/database.php';

class Devotion {
    private $db;
    
    public function __construct() {
        $this->db = getDatabaseConnection();
    }
    
    /**
     * Find devotion by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM devotions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find devotion by date
     */
    public function findByDate($date) {
        $stmt = $this->db->prepare("SELECT * FROM devotions WHERE devotion_date = ? LIMIT 1");
        $stmt->execute([$date]);
        return $stmt->fetch();
    }
    
    /**
     * Create new devotion
     */
    public function create($date, $scriptureReferences = null, $verseText = null, $authorParagraphsEnabled = false) {
        $stmt = $this->db->prepare("
            INSERT INTO devotions (devotion_date, scripture_references, verse_text, author_paragraphs_enabled) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([$date, $scriptureReferences, $verseText, $authorParagraphsEnabled ? 1 : 0]);
    }
    
    /**
     * Update devotion
     */
    public function update($id, $data) {
        $allowedFields = ['devotion_date', 'scripture_references', 'verse_text', 'author_paragraphs_enabled'];
        $updates = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                if ($field === 'author_paragraphs_enabled') {
                    $value = $value ? 1 : 0;
                }
                $updates[] = "$field = ?";
                $values[] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE devotions SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Delete devotion
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM devotions WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get all devotions
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM devotions ORDER BY devotion_date DESC";
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Get paragraphs for devotion
     */
    public function getParagraphs($devotionId) {
        $stmt = $this->db->prepare("
            SELECT id, paragraph_number, content 
            FROM devotion_paragraphs 
            WHERE devotion_id = ? 
            ORDER BY paragraph_number ASC
        ");
        $stmt->execute([$devotionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Add paragraph to devotion
     */
    public function addParagraph($devotionId, $paragraphNumber, $content = null) {
        $stmt = $this->db->prepare("
            INSERT INTO devotion_paragraphs (devotion_id, paragraph_number, content) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$devotionId, $paragraphNumber, $content]);
    }
    
    /**
     * Update paragraph
     */
    public function updateParagraph($id, $content) {
        $stmt = $this->db->prepare("UPDATE devotion_paragraphs SET content = ? WHERE id = ?");
        return $stmt->execute([$content, $id]);
    }
    
    /**
     * Delete paragraph
     */
    public function deleteParagraph($id) {
        $stmt = $this->db->prepare("DELETE FROM devotion_paragraphs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

