<?php
/**
 * Front Controller - Simple Router
 */

// Set timezone
date_default_timezone_set('UTC');

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoload includes
require_once BASE_PATH . '/includes/database.php';
require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/includes/csrf.php';
require_once BASE_PATH . '/includes/validation.php';

// Autoload models
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Devotion.php';
require_once BASE_PATH . '/models/Reading.php';
require_once BASE_PATH . '/models/Comment.php';
require_once BASE_PATH . '/models/CommentService.php';

// Autoload controllers
require_once BASE_PATH . '/controllers/AuthController.php';
require_once BASE_PATH . '/controllers/DevotionController.php';
require_once BASE_PATH . '/controllers/CommentController.php';
require_once BASE_PATH . '/controllers/AdminController.php';

// Get request URI and method
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string
$requestUri = strtok($requestUri, '?');

// Remove base path if in subdirectory
$basePath = '/';
$requestUri = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $requestUri);

// Parse route
$segments = explode('/', trim($requestUri, '/'));

// Route mapping
$controller = null;
$action = null;
$params = [];

if (empty($segments[0]) || $segments[0] === 'index.php') {
    // Home - redirect to today's devotion
    $controller = new DevotionController();
    $controller->index();
    exit;
}

switch ($segments[0]) {
    case 'login':
        $controller = new AuthController();
        if ($requestMethod === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'register':
        $controller = new AuthController();
        if ($requestMethod === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;
        
    case 'devotion':
        $controller = new DevotionController();
        if (isset($segments[1]) && $segments[1] === 'today') {
            $controller->today();
        } elseif (isset($segments[1]) && $segments[1] === 'date' && isset($segments[2])) {
            $controller->showByDate($segments[2]);
        } else {
            $controller->index();
        }
        break;
        
    case 'api':
        if (isset($segments[1]) && $segments[1] === 'comments') {
            $controller = new CommentController();
            if ($requestMethod === 'GET' && isset($segments[2])) {
                $controller->getComments($segments[2]);
            } elseif ($requestMethod === 'POST') {
                $controller->create();
            } elseif (($requestMethod === 'PUT' || ($_POST['_method'] ?? '') === 'PUT') && isset($segments[2])) {
                // Parse PUT data
                if ($requestMethod === 'PUT') {
                    parse_str(file_get_contents('php://input'), $_PUT);
                    $_POST = array_merge($_POST, $_PUT);
                }
                $controller->update($segments[2]);
            } elseif (($requestMethod === 'DELETE' || ($_POST['_method'] ?? '') === 'DELETE') && isset($segments[2])) {
                // Parse DELETE data
                if ($requestMethod === 'DELETE') {
                    parse_str(file_get_contents('php://input'), $_DELETE);
                    $_POST = array_merge($_POST, $_DELETE);
                }
                $controller->delete($segments[2]);
            }
        }
        break;
        
    case 'admin':
        $controller = new AdminController();
        if (isset($segments[1]) && $segments[1] === 'devotions') {
            if ($requestMethod === 'GET' && (!isset($segments[2]) || $segments[2] === '')) {
                $controller->listDevotions();
            } elseif ($requestMethod === 'GET' && $segments[2] === 'new') {
                $controller->showCreateDevotion();
            } elseif ($requestMethod === 'POST' && (!isset($segments[2]) || $segments[2] === '')) {
                $controller->createDevotion();
            } elseif ($requestMethod === 'GET' && isset($segments[2]) && $segments[2] !== 'new' && isset($segments[3]) && $segments[3] === 'edit') {
                $controller->showEditDevotion($segments[2]);
            } elseif (($requestMethod === 'POST' && ($_POST['_method'] ?? '') === 'PUT') && isset($segments[2]) && isset($segments[3]) && $segments[3] === 'edit') {
                $controller->updateDevotion($segments[2]);
            } elseif ($requestMethod === 'POST' && isset($segments[2]) && isset($segments[3]) && $segments[3] === 'delete') {
                $controller->deleteDevotion($segments[2]);
            } elseif ($requestMethod === 'POST' && (!isset($segments[2]) || $segments[2] === '')) {
                $controller->createDevotion();
            }
        } elseif (isset($segments[1]) && $segments[1] === 'users') {
            if ($requestMethod === 'GET' && (!isset($segments[2]) || $segments[2] === '')) {
                $controller->listUsers();
            } elseif (($requestMethod === 'PUT' || ($_POST['_method'] ?? '') === 'PUT') && isset($segments[2]) && isset($segments[3]) && $segments[3] === 'role') {
                if ($requestMethod === 'PUT') {
                    parse_str(file_get_contents('php://input'), $_PUT);
                    $_POST = array_merge($_POST, $_PUT);
                }
                $controller->updateUserRole($segments[2]);
            }
        } elseif (isset($segments[1]) && $segments[1] === 'comments' && isset($segments[2]) && isset($segments[3]) && $segments[3] === 'moderate') {
            if ($requestMethod === 'POST') {
                // Already in $_POST
            } else {
                parse_str(file_get_contents('php://input'), $_POST);
            }
            $controller->moderateComment($segments[2]);
        }
        break;
        
    default:
        http_response_code(404);
        die('Page not found');
}

