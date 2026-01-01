<?php
/**
 * Authentication Helper Functions
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/app.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    $appConfig = require __DIR__ . '/../config/app.php';
    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => $isSecure,
        'cookie_samesite' => 'Strict'
    ]);
}

/**
 * Get current user from session
 */
function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $userModel = new User();
    return $userModel->findById($_SESSION['user_id']);
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication (redirect to login if not authenticated)
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /login');
        exit;
    }
}

/**
 * Require specific role
 */
function requireRole($requiredRole) {
    requireAuth();
    
    $user = getCurrentUser();
    if (!$user || $user['role'] !== $requiredRole) {
        http_response_code(403);
        die('Access denied. This page requires ' . User::getRoleLabel($requiredRole) . ' role.');
    }
}

/**
 * Login user
 */
function loginUser($userId) {
    $_SESSION['user_id'] = $userId;
    session_regenerate_id(true);
    
    $userModel = new User();
    $userModel->updateLastLogin($userId);
}

/**
 * Logout user
 */
function logoutUser() {
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Check if user can edit comment (ownership + time window)
 */
function canEditComment($comment, $editWindowMinutes = 30) {
    if (!$comment) {
        return false;
    }
    
    $user = getCurrentUser();
    if (!$user) {
        return false;
    }
    
    // Check ownership
    if ($comment['user_id'] != $user['id']) {
        return false;
    }
    
    // Check time window
    $createdAt = strtotime($comment['created_at']);
    $now = time();
    $windowSeconds = $editWindowMinutes * 60;
    
    return ($now - $createdAt) <= $windowSeconds;
}

/**
 * Check if user can delete comment (ownership or site_pastor)
 */
function canDeleteComment($comment) {
    if (!$comment) {
        return false;
    }
    
    $user = getCurrentUser();
    if (!$user) {
        return false;
    }
    
    // Site pastor can delete any comment
    if ($user['role'] === 'site_pastor') {
        return true;
    }
    
    // Users can delete their own comments
    return $comment['user_id'] == $user['id'];
}

