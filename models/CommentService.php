<?php
/**
 * Comment Service - Handles comment business logic including depth validation
 */

require_once __DIR__ . '/Comment.php';

class CommentService {
    private $commentModel;
    private $maxDepth = 2; // 3 levels total: 0, 1, 2
    
    public function __construct() {
        $this->commentModel = new Comment();
    }
    
    /**
     * Validate comment depth before creation
     */
    public function validateDepth($parentCommentId) {
        if ($parentCommentId === null) {
            return ['valid' => true, 'depth' => 0];
        }
        
        $parent = $this->commentModel->getParent($parentCommentId);
        
        if (!$parent) {
            return ['valid' => false, 'error' => 'Parent comment not found'];
        }
        
        if ($parent['depth'] >= $this->maxDepth) {
            return ['valid' => false, 'error' => 'Maximum comment depth reached'];
        }
        
        $newDepth = $parent['depth'] + 1;
        
        if ($newDepth > $this->maxDepth) {
            return ['valid' => false, 'error' => 'Cannot reply: maximum depth exceeded'];
        }
        
        return ['valid' => true, 'depth' => $newDepth];
    }
    
    /**
     * Create comment with depth validation
     */
    public function createComment($devotionId, $userId, $body, $parentCommentId = null) {
        $validation = $this->validateDepth($parentCommentId);
        
        if (!$validation['valid']) {
            throw new Exception($validation['error']);
        }
        
        $depth = $validation['depth'];
        
        $result = $this->commentModel->create($devotionId, $userId, $body, $parentCommentId, $depth);
        
        if (!$result) {
            throw new Exception('Failed to create comment');
        }
        
        return $this->commentModel->getLastInsertId();
    }
    
    /**
     * Get comment tree for devotion
     */
    public function getCommentTree($devotionId) {
        $comments = $this->commentModel->getByDevotionId($devotionId);
        return $this->commentModel->buildTree($comments);
    }
}

