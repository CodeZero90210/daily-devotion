<?php
/**
 * Devotion Controller
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Devotion.php';
require_once __DIR__ . '/../models/Reading.php';
require_once __DIR__ . '/../models/CommentService.php';
require_once __DIR__ . '/../config/app.php';

class DevotionController {
    private $devotionModel;
    private $readingModel;
    private $commentService;
    private $appConfig;
    
    public function __construct() {
        $this->devotionModel = new Devotion();
        $this->readingModel = new Reading();
        $this->commentService = new CommentService();
        $this->appConfig = require __DIR__ . '/../config/app.php';
    }
    
    /**
     * Redirect to today's devotion
     */
    public function index() {
        $today = date('Y-m-d');
        header("Location: /devotion/date/$today");
        exit;
    }
    
    /**
     * Show today's devotion
     */
    public function today() {
        $today = date('Y-m-d');
        $this->showByDate($today);
    }
    
    /**
     * Show devotion by date
     */
    public function showByDate($date) {
        requireAuth();
        
        if (!validateDate($date)) {
            http_response_code(400);
            die('Invalid date format');
        }
        
        $devotion = $this->devotionModel->findByDate($date);
        
        if (!$devotion) {
            http_response_code(404);
            die('Devotion not found for this date');
        }
        
        // Get readings
        $readings = $this->readingModel->getByDevotionId($devotion['id']);
        
        // Get paragraphs
        $paragraphs = $this->devotionModel->getParagraphs($devotion['id']);
        
        // Get comments tree
        $comments = $this->commentService->getCommentTree($devotion['id']);
        
        // Check copyright mode
        $showText = ($this->appConfig['copyright_mode'] === 'enabled' && 
                     $devotion['author_paragraphs_enabled']);
        
        $user = getCurrentUser();
        $csrfToken = getCSRFToken();
        
        require __DIR__ . '/../views/devotions/show.php';
    }
}

