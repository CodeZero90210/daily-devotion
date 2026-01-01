<?php
/**
 * CSRF Protection Helper Functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get current CSRF token
 */
function getCSRFToken() {
    return generateCSRFToken();
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Require valid CSRF token (for POST/PUT/DELETE requests)
 */
function requireCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return true;
    }
    
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    
    if (!$token || !validateCSRFToken($token)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
}

