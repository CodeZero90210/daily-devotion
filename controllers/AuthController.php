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
}

