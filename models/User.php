<?php
/**
 * User Model
 */

require_once __DIR__ . '/../includes/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDatabaseConnection();
    }
    
    /**
     * Find user by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Create new user
     */
    public function create($email, $password, $displayName, $role = 'brother') {
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
        if ($passwordHash === false) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password_hash, display_name, role) 
            VALUES (?, ?, ?, ?)
        ");
        
        return $stmt->execute([$email, $passwordHash, $displayName, $role]);
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $allowedFields = ['email', 'display_name', 'role'];
        $updates = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "$field = ?";
                $values[] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin($id) {
        $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Get all users
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT id, email, display_name, role, created_at, last_login_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Get role display label
     */
    public static function getRoleLabel($role) {
        $labels = [
            'site_pastor' => 'Site Pastor',
            'brother' => 'Brother',
            'sister' => 'Sister'
        ];
        return $labels[$role] ?? $role;
    }
}

