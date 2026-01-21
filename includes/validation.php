<?php
/**
 * Validation Helper Functions
 */

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    if (strlen($password) < 12) {
        return ['valid' => false, 'error' => 'Password must be at least 12 characters long'];
    }
    return ['valid' => true];
}

/**
 * Sanitize string input
 */
function sanitizeString($input) {
    return trim(strip_tags($input));
}

/**
 * Escape output for HTML
 */
function escapeHtml($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Format comment body (preserve line breaks, strip HTML)
 */
function formatCommentBody($body) {
    $body = strip_tags($body);
    $body = nl2br(escapeHtml($body));
    return $body;
}

/**
 * Validate date format (YYYY-MM-DD)
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Format devotion date for display (Month Day only, no year)
 * Example: "January 1", "December 25"
 * 
 * @param string $date Date in YYYY-MM-DD format
 * @return string Formatted date string (F j format)
 */
function formatDevotionDate($date) {
    return date('F j', strtotime($date));
}
