<?php
/**
 * Admin Controller (site_pastor only)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/validation.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Devotion.php';
require_once __DIR__ . '/../models/Reading.php';
require_once __DIR__ . '/../models/Comment.php';

class AdminController {
    private $userModel;
    private $devotionModel;
    private $readingModel;
    private $commentModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->devotionModel = new Devotion();
        $this->readingModel = new Reading();
        $this->commentModel = new Comment();
    }
    
    /**
     * List all devotions
     */
    public function listDevotions() {
        requireRole('site_pastor');
        
        $devotions = $this->devotionModel->getAll();
        $csrfToken = getCSRFToken();
        
        require __DIR__ . '/../views/admin/devotions.php';
    }
    
    /**
     * Show create devotion form
     */
    public function showCreateDevotion() {
        requireRole('site_pastor');
        
        $csrfToken = getCSRFToken();
        require __DIR__ . '/../views/admin/devotion_form.php';
    }
    
    /**
     * Create devotion
     */
    public function createDevotion() {
        requireRole('site_pastor');
        requireCSRF();
        
        $date = $_POST['devotion_date'] ?? '';
        $scriptureReferences = $_POST['scripture_references'] ?? null;
        $verseText = $_POST['verse_text'] ?? null;
        $authorParagraphsEnabled = isset($_POST['author_paragraphs_enabled']) ? true : false;
        
        if (empty($date) || !validateDate($date)) {
            $_SESSION['error'] = 'Valid date is required';
            header('Location: /admin/devotions/new');
            exit;
        }
        
        // Create devotion
        if ($this->devotionModel->create($date, $scriptureReferences, $verseText, $authorParagraphsEnabled)) {
            $devotionId = $this->devotionModel->findByDate($date)['id'];
            
            // Handle readings
            if (isset($_POST['readings']) && is_array($_POST['readings'])) {
                foreach ($_POST['readings'] as $order => $reference) {
                    if (!empty($reference)) {
                        $this->readingModel->create($devotionId, $order + 1, sanitizeString($reference));
                    }
                }
            }
            
            $_SESSION['success'] = 'Devotion created successfully';
            header('Location: /admin/devotions');
            exit;
        } else {
            $_SESSION['error'] = 'Failed to create devotion';
            header('Location: /admin/devotions/new');
            exit;
        }
    }
    
    /**
     * Show edit devotion form
     */
    public function showEditDevotion($id) {
        requireRole('site_pastor');
        
        $devotion = $this->devotionModel->findById($id);
        if (!$devotion) {
            http_response_code(404);
            die('Devotion not found');
        }
        
        $readings = $this->readingModel->getByDevotionId($id);
        $csrfToken = getCSRFToken();
        
        require __DIR__ . '/../views/admin/devotion_form.php';
    }
    
    /**
     * Update devotion
     */
    public function updateDevotion($id) {
        requireRole('site_pastor');
        requireCSRF();
        
        $devotion = $this->devotionModel->findById($id);
        if (!$devotion) {
            http_response_code(404);
            die('Devotion not found');
        }
        
        $date = $_POST['devotion_date'] ?? '';
        $scriptureReferences = $_POST['scripture_references'] ?? null;
        $verseText = $_POST['verse_text'] ?? null;
        $authorParagraphsEnabled = isset($_POST['author_paragraphs_enabled']) ? true : false;
        
        if (empty($date) || !validateDate($date)) {
            $_SESSION['error'] = 'Valid date is required';
            header("Location: /admin/devotions/$id/edit");
            exit;
        }
        
        // Update devotion
        $updateData = [
            'devotion_date' => $date,
            'scripture_references' => $scriptureReferences,
            'verse_text' => $verseText,
            'author_paragraphs_enabled' => $authorParagraphsEnabled
        ];
        
        if ($this->devotionModel->update($id, $updateData)) {
            // Update readings (delete old, insert new)
            $this->readingModel->deleteByDevotionId($id);
            
            if (isset($_POST['readings']) && is_array($_POST['readings'])) {
                foreach ($_POST['readings'] as $order => $reference) {
                    if (!empty($reference)) {
                        $this->readingModel->create($id, $order + 1, sanitizeString($reference));
                    }
                }
            }
            
            $_SESSION['success'] = 'Devotion updated successfully';
            header('Location: /admin/devotions');
            exit;
        } else {
            $_SESSION['error'] = 'Failed to update devotion';
            header("Location: /admin/devotions/$id/edit");
            exit;
        }
    }
    
    /**
     * Delete devotion
     */
    public function deleteDevotion($id) {
        requireRole('site_pastor');
        requireCSRF();
        
        if ($this->devotionModel->delete($id)) {
            $_SESSION['success'] = 'Devotion deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete devotion';
        }
        
        header('Location: /admin/devotions');
        exit;
    }
    
    /**
     * List all users
     */
    public function listUsers() {
        requireRole('site_pastor');
        
        $users = $this->userModel->getAll();
        $csrfToken = getCSRFToken();
        
        require __DIR__ . '/../views/admin/users.php';
    }
    
    /**
     * Update user role
     */
    public function updateUserRole($userId) {
        requireRole('site_pastor');
        requireCSRF();
        
        $role = $_POST['role'] ?? '';
        
        if (!in_array($role, ['site_pastor', 'brother', 'sister'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid role']);
            exit;
        }
        
        if ($this->userModel->update($userId, ['role' => $role])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update user role']);
        }
    }
    
    /**
     * Moderate comment (soft delete)
     */
    public function moderateComment($commentId) {
        requireRole('site_pastor');
        requireCSRF();
        
        if ($this->commentModel->softDelete($commentId)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to moderate comment']);
        }
    }
}

