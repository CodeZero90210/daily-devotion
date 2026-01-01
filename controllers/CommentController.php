<?php
/**
 * Comment Controller
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/validation.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/CommentService.php';
require_once __DIR__ . '/../config/app.php';

class CommentController {
    private $commentModel;
    private $commentService;
    private $appConfig;
    
    public function __construct() {
        $this->commentModel = new Comment();
        $this->commentService = new CommentService();
        $this->appConfig = require __DIR__ . '/../config/app.php';
    }
    
    /**
     * Get comments for devotion (JSON API)
     */
    public function getComments($devotionId) {
        requireAuth();
        
        $comments = $this->commentService->getCommentTree($devotionId);
        
        header('Content-Type: application/json');
        echo json_encode($comments);
    }
    
    /**
     * Create comment
     */
    public function create() {
        requireAuth();
        requireCSRF();
        
        $devotionId = $_POST['devotion_id'] ?? null;
        $parentCommentId = $_POST['parent_comment_id'] ?? null;
        $body = $_POST['body'] ?? '';
        
        if (empty($devotionId) || empty($body)) {
            http_response_code(400);
            echo json_encode(['error' => 'devotion_id and body are required']);
            exit;
        }
        
        $body = sanitizeString($body);
        if (empty($body)) {
            http_response_code(400);
            echo json_encode(['error' => 'Comment body cannot be empty']);
            exit;
        }
        
        $user = getCurrentUser();
        
        try {
            // Convert empty string to null for parent_comment_id
            if ($parentCommentId === '') {
                $parentCommentId = null;
            }
            
            $commentId = $this->commentService->createComment(
                $devotionId,
                $user['id'],
                $body,
                $parentCommentId
            );
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'comment_id' => $commentId]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Update comment
     */
    public function update($commentId) {
        requireAuth();
        requireCSRF();
        
        $body = $_POST['body'] ?? '';
        
        if (empty($body)) {
            http_response_code(400);
            echo json_encode(['error' => 'Comment body cannot be empty']);
            exit;
        }
        
        $comment = $this->commentModel->findById($commentId);
        
        if (!$comment) {
            http_response_code(404);
            echo json_encode(['error' => 'Comment not found']);
            exit;
        }
        
        // Check ownership
        $user = getCurrentUser();
        if ($comment['user_id'] != $user['id']) {
            http_response_code(403);
            echo json_encode(['error' => 'You can only edit your own comments']);
            exit;
        }
        
        // Check edit window
        $editWindowMinutes = $this->appConfig['comment_edit_window_minutes'];
        if (!$this->commentModel->canBeEdited($commentId, $editWindowMinutes)) {
            http_response_code(403);
            echo json_encode(['error' => 'Edit window has expired']);
            exit;
        }
        
        $body = sanitizeString($body);
        
        if ($this->commentModel->update($commentId, $body)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update comment']);
        }
    }
    
    /**
     * Delete comment (soft delete)
     */
    public function delete($commentId) {
        requireAuth();
        requireCSRF();
        
        $comment = $this->commentModel->findById($commentId);
        
        if (!$comment) {
            http_response_code(404);
            echo json_encode(['error' => 'Comment not found']);
            exit;
        }
        
        // Check permissions
        if (!canDeleteComment($comment)) {
            http_response_code(403);
            echo json_encode(['error' => 'You do not have permission to delete this comment']);
            exit;
        }
        
        if ($this->commentModel->softDelete($commentId)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete comment']);
        }
    }
}

