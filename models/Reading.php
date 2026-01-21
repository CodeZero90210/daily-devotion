<?php
/**
 * Reading Model
 */

require_once __DIR__ . '/../includes/database.php';

class Reading {
    private $db;
    
    // Valid reading categories
    const CATEGORIES = [
        'Old Testament',
        'New Testament',
        'Psalms',
        'Proverbs'
    ];
    
    // Category display order
    const CATEGORY_ORDER = [
        'Old Testament' => 1,
        'New Testament' => 2,
        'Psalms' => 3,
        'Proverbs' => 4
    ];
    
    public function __construct() {
        $this->db = getDatabaseConnection();
    }
    
    /**
     * Get readings for devotion, ordered by category
     */
    public function getByDevotionId($devotionId) {
        $stmt = $this->db->prepare("
            SELECT id, category, scripture_reference 
            FROM readings 
            WHERE devotion_id = ? 
            ORDER BY 
                CASE category
                    WHEN 'Old Testament' THEN 1
                    WHEN 'New Testament' THEN 2
                    WHEN 'Psalms' THEN 3
                    WHEN 'Proverbs' THEN 4
                    ELSE 5
                END ASC
        ");
        $stmt->execute([$devotionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get reading by devotion ID and category
     */
    public function getByDevotionIdAndCategory($devotionId, $category) {
        $stmt = $this->db->prepare("
            SELECT id, category, scripture_reference 
            FROM readings 
            WHERE devotion_id = ? AND category = ?
        ");
        $stmt->execute([$devotionId, $category]);
        return $stmt->fetch();
    }
    
    /**
     * Create or update reading by category
     */
    public function upsertByCategory($devotionId, $category, $scriptureReference) {
        // Validate category
        if (!in_array($category, self::CATEGORIES)) {
            throw new InvalidArgumentException("Invalid category: $category");
        }
        
        // Check if reading exists for this devotion and category
        $existing = $this->getByDevotionIdAndCategory($devotionId, $category);
        
        if ($existing) {
            // Update existing reading
            $stmt = $this->db->prepare("
                UPDATE readings 
                SET scripture_reference = ? 
                WHERE devotion_id = ? AND category = ?
            ");
            return $stmt->execute([$scriptureReference, $devotionId, $category]);
        } else {
            // Create new reading
            $stmt = $this->db->prepare("
                INSERT INTO readings (devotion_id, category, scripture_reference) 
                VALUES (?, ?, ?)
            ");
            return $stmt->execute([$devotionId, $category, $scriptureReference]);
        }
    }
    
    /**
     * Add reading to devotion (legacy method - uses category now)
     */
    public function create($devotionId, $readingOrder, $scriptureReference) {
        // Map reading_order to category for backward compatibility
        $categoryMap = [
            1 => 'Old Testament',
            2 => 'New Testament',
            3 => 'Psalms',
            4 => 'Proverbs'
        ];
        
        $category = $categoryMap[$readingOrder] ?? 'Old Testament';
        return $this->upsertByCategory($devotionId, $category, $scriptureReference);
    }
    
    /**
     * Update reading
     */
    public function update($id, $readingOrder, $scriptureReference) {
        // For backward compatibility, but this should use upsertByCategory instead
        $stmt = $this->db->prepare("
            UPDATE readings 
            SET scripture_reference = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$scriptureReference, $id]);
    }
    
    /**
     * Delete reading
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM readings WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Delete reading by devotion ID and category
     */
    public function deleteByDevotionIdAndCategory($devotionId, $category) {
        $stmt = $this->db->prepare("DELETE FROM readings WHERE devotion_id = ? AND category = ?");
        return $stmt->execute([$devotionId, $category]);
    }
    
    /**
     * Delete all readings for devotion
     */
    public function deleteByDevotionId($devotionId) {
        $stmt = $this->db->prepare("DELETE FROM readings WHERE devotion_id = ?");
        return $stmt->execute([$devotionId]);
    }
}

