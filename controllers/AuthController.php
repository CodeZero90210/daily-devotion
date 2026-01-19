<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/validation.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    /**
     * Show login form
     */
    public function showLogin() {
        if (isAuthenticated()) {
            header('Location: /');
            exit;
        }
        
        $csrfToken = getCSRFToken();
        require __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Process login
     */
    public function login() {
        requireCSRF();
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            header('Location: /login');
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user || !$userModel->verifyPassword($password, $user['password_hash'])) {
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: /login');
            exit;
        }
        
        loginUser($user['id']);
        header('Location: /');
        exit;
    }
    
    /**
     * Logout
     */
    public function logout() {
        requireCSRF();
        logoutUser();
        header('Location: /login');
        exit;
    }
    
    /**
     * Show registration form
     */
    public function showRegister() {
        if (isAuthenticated()) {
            header('Location: /');
            exit;
        }
        
        $csrfToken = getCSRFToken();
        require __DIR__ . '/../views/auth/register.php';
    }
    
    /**
     * Process registration
     */
    public function register() {
        requireCSRF();
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $displayName = $_POST['display_name'] ?? '';
        $role = $_POST['role'] ?? 'brother';
        
        // Validate input
        if (empty($email) || empty($password) || empty($displayName)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /register');
            exit;
        }
        
        if (!validateEmail($email)) {
            $_SESSION['error'] = 'Invalid email address';
            header('Location: /register');
            exit;
        }
        
        $passwordValidation = validatePassword($password);
        if (!$passwordValidation['valid']) {
            $_SESSION['error'] = $passwordValidation['error'];
            header('Location: /register');
            exit;
        }
        
        // Validate role
        if (!in_array($role, ['brother', 'sister'])) {
            $role = 'brother';
        }
        
        $userModel = new User();
        
        // Check if email already exists
        if ($userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Email already registered';
            header('Location: /register');
            exit;
        }
        
        // Create user
        if ($userModel->create($email, $password, $displayName, $role)) {
            $_SESSION['success'] = 'Registration successful. Please login.';
            header('Location: /login');
            exit;
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            header('Location: /register');
            exit;
        }
    }
    
    /**
     * Show forgot password form
     */
    public function showForgotPassword() {
        if (isAuthenticated()) {
            header('Location: /');
            exit;
        }
        
        $csrfToken = getCSRFToken();
        require __DIR__ . '/../views/auth/forgot_password.php';
    }
    
    /**
     * Process forgot password request
     */
    public function requestPasswordReset() {
        requireCSRF();
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $_SESSION['error'] = 'Email is required';
            header('Location: /forgot-password');
            exit;
        }
        
        if (!validateEmail($email)) {
            $_SESSION['error'] = 'Invalid email address';
            header('Location: /forgot-password');
            exit;
        }
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        // Always show success message to prevent email enumeration
        if ($user) {
            $token = $userModel->createPasswordResetToken($user['id']);
            
            if ($token) {
                // In a production environment, you would send an email here
                // For now, we'll show the reset link directly
                $resetUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') 
                    . '://' . $_SERVER['HTTP_HOST'] . '/reset-password?token=' . urlencode($token);
                
                $_SESSION['success'] = 'Password reset link has been generated.';
                $_SESSION['reset_url'] = $resetUrl;
            } else {
                $_SESSION['error'] = 'Failed to generate reset token. Please try again.';
            }
        } else {
            // Show generic success message even if user doesn't exist
            $_SESSION['success'] = 'If an account exists with that email, a password reset link has been sent.';
        }
        
        header('Location: /forgot-password');
        exit;
    }
    
    /**
     * Show reset password form
     */
    public function showResetPassword() {
        if (isAuthenticated()) {
            header('Location: /');
            exit;
        }
        
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['error'] = 'Invalid or missing reset token';
            header('Location: /forgot-password');
            exit;
        }
        
        $userModel = new User();
        $tokenData = $userModel->findPasswordResetToken($token);
        
        if (!$tokenData) {
            $_SESSION['error'] = 'Invalid or expired reset token. Please request a new one.';
            header('Location: /forgot-password');
            exit;
        }
        
        $csrfToken = getCSRFToken();
        require __DIR__ . '/../views/auth/reset_password.php';
    }
    
    /**
     * Process password reset
     */
    public function resetPassword() {
        requireCSRF();
        
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        if (empty($token) || empty($password) || empty($passwordConfirm)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /reset-password?token=' . urlencode($token));
            exit;
        }
        
        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: /reset-password?token=' . urlencode($token));
            exit;
        }
        
        $passwordValidation = validatePassword($password);
        if (!$passwordValidation['valid']) {
            $_SESSION['error'] = $passwordValidation['error'];
            header('Location: /reset-password?token=' . urlencode($token));
            exit;
        }
        
        $userModel = new User();
        $tokenData = $userModel->findPasswordResetToken($token);
        
        if (!$tokenData) {
            $_SESSION['error'] = 'Invalid or expired reset token. Please request a new one.';
            header('Location: /forgot-password');
            exit;
        }
        
        // Update password
        if ($userModel->updatePassword($tokenData['user_id'], $password)) {
            // Mark token as used
            $userModel->markPasswordResetTokenAsUsed($token);
            
            $_SESSION['success'] = 'Password has been reset successfully. Please login with your new password.';
            header('Location: /login');
            exit;
        } else {
            $_SESSION['error'] = 'Failed to reset password. Please try again.';
            header('Location: /reset-password?token=' . urlencode($token));
            exit;
        }
    }
}

