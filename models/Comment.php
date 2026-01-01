<?php
/**
 * Comment Model
 */

require_once __DIR__ . '/../includes/database.php';

class Comment {
    private $db;
    
    public function __construct() {
        $this->db = getDatabaseConnection();
    }
    
    /**
     * Find comment by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get all comments for devotion (flat list)
     */
    public function getByDevotionId($devotionId) {
        $stmt = $this->db->prepare("
            SELECT 
                c.id, c.parent_comment_id, c.depth, c.body, c.created_at, 
                c.updated_at, c.edited_at, c.is_deleted,
                u.id as user_id, u.display_name, u.role
            FROM comments c
            INNER JOIN users u ON c.user_id = u.id
            WHERE c.devotion_id = ? AND c.is_deleted = FALSE
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$devotionId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create new comment
     */
    public function create($devotionId, $userId, $body, $parentCommentId = null, $depth = 0) {
        $stmt = $this->db->prepare("
            INSERT INTO comments (devotion_id, user_id, parent_comment_id, depth, body) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([$devotionId, $userId, $parentCommentId, $depth, $body]);
    }
    
    /**
     * Get last insert ID
     */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }
    
    /**
     * Update comment
     */
    public function update($id, $body) {
        $stmt = $this->db->prepare("
            UPDATE comments 
            SET body = ?, edited_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$body, $id]);
    }
    
    /**
     * Soft delete comment
     */
    public function softDelete($id) {
        $stmt = $this->db->prepare("
            UPDATE comments 
            SET is_deleted = TRUE, deleted_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get parent comment
     */
    public function getParent($parentCommentId) {
        if ($parentCommentId === null) {
            return null;
        }
        return $this->findById($parentCommentId);
    }
    
    /**
     * Check if comment can be edited (within time window)
     */
    public function canBeEdited($commentId, $editWindowMinutes = 30) {
        $comment = $this->findById($commentId);
        if (!$comment) {
            return false;
        }
        
        $createdAt = strtotime($comment['created_at']);
        $now = time();
        $windowSeconds = $editWindowMinutes * 60;
        
        return ($now - $createdAt) <= $windowSeconds;
    }
    
    /**
     * Build comment tree from flat list
     */
    public function buildTree($comments) {
        $map = [];
        $roots = [];
        
        // Create map of all comments
        foreach ($comments as $comment) {
            $comment['children'] = [];
            $map[$comment['id']] = $comment;
        }
        
        // Build tree structure
        foreach ($map as $id => $comment) {
            if ($comment['parent_comment_id'] === null) {
                $roots[] = &$map[$id];
            } else {
                if (isset($map[$comment['parent_comment_id']])) {
                    $map[$comment['parent_comment_id']]['children'][] = &$map[$id];
                }
            }
        }
        
        return $roots;
    }
}

