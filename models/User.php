<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    public function getUserById($userId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function getUserByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function createUser($username, $email, $password, $firstName = '', $lastName = '', $currency = 'USD') {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, first_name, last_name, currency) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $email, $password, $firstName, $lastName, $currency);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    public function updateProfile($userId, $firstName, $lastName, $currency, $theme) {
        $stmt = $this->db->prepare("UPDATE users SET first_name = ?, last_name = ?, currency = ?, theme = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $firstName, $lastName, $currency, $theme, $userId);
        
        return $stmt->execute();
    }
    
    public function updatePassword($userId, $password) {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password, $userId);
        
        return $stmt->execute();
    }
    
    public function createPasswordResetToken($userId, $token, $expiresAt) {
        // Delete any existing tokens for this user
        $stmt = $this->db->prepare("DELETE FROM password_reset_tokens WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        // Create new token
        $stmt = $this->db->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $token, $expiresAt);
        
        return $stmt->execute();
    }
    
    public function getPasswordResetToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM password_reset_tokens WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function deletePasswordResetToken($token) {
        $stmt = $this->db->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
        $stmt->bind_param("s", $token);
        
        return $stmt->execute();
    }
}
?>