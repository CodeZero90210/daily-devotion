<?php
/**
 * Reading Model
 */

require_once __DIR__ . '/../includes/database.php';

class Reading {
    private $db;
    
    public function __construct() {
        $this->db = getDatabaseConnection();
    }
    
    /**
     * Get readings for devotion
     */
    public function getByDevotionId($devotionId) {
        $stmt = $this->db->prepare("
            SELECT id, reading_order, scripture_reference 
            FROM readings 
            WHERE devotion_id = ? 
            ORDER BY reading_order ASC
        ");
        $stmt->execute([$devotionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Add reading to devotion
     */
    public function create($devotionId, $readingOrder, $scriptureReference) {
        $stmt = $this->db->prepare("
            INSERT INTO readings (devotion_id, reading_order, scripture_reference) 
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$devotionId, $readingOrder, $scriptureReference]);
    }
    
    /**
     * Update reading
     */
    public function update($id, $readingOrder, $scriptureReference) {
        $stmt = $this->db->prepare("
            UPDATE readings 
            SET reading_order = ?, scripture_reference = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$readingOrder, $scriptureReference, $id]);
    }
    
    /**
     * Delete reading
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM readings WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Delete all readings for devotion
     */
    public function deleteByDevotionId($devotionId) {
        $stmt = $this->db->prepare("DELETE FROM readings WHERE devotion_id = ?");
        return $stmt->execute([$devotionId]);
    }
}

