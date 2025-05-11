<?php
require_once 'models/User.php';

class SettingsController {
    private $userModel;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->userModel = new User();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get user details
        $user = $this->userModel->getUserById($userId);
        
        // Available currencies
        $currencies = [
            'USD' => 'US Dollar ($)',
            'EUR' => 'Euro (€)',
            'GBP' => 'British Pound (£)',
            'JPY' => 'Japanese Yen (¥)',
            'INR' => 'Indian Rupee (₹)',
            'CNY' => 'Chinese Yuan (¥)',
            'CAD' => 'Canadian Dollar ($)',
            'AUD' => 'Australian Dollar ($)',
            'CHF' => 'Swiss Franc (Fr)',
            'SGD' => 'Singapore Dollar ($)'
        ];
        
        // Load settings view
        require_once 'views/settings/index.php';
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=settings');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $currency = $_POST['currency'] ?? DEFAULT_CURRENCY;
        $theme = $_POST['theme'] ?? 'light';
        
        // Update profile
        $success = $this->userModel->updateProfile($userId, $firstName, $lastName, $currency, $theme);
        
        if ($success) {
            // Update session variables
            $_SESSION['currency'] = $currency;
            $_SESSION['theme'] = $theme;
            
            $_SESSION['success'] = "Settings updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update settings";
        }
        
        // Check if password change was requested
        if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            // Validate passwords
            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = "New passwords do not match";
                header('Location: index.php?page=settings');
                exit;
            }
            
            // Verify current password
            $user = $this->userModel->getUserById($userId);
            
            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['error'] = "Current password is incorrect";
                header('Location: index.php?page=settings');
                exit;
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $passwordSuccess = $this->userModel->updatePassword($userId, $hashedPassword);
            
            if ($passwordSuccess) {
                $_SESSION['success'] = "Password updated successfully";
            } else {
                $_SESSION['error'] = "Failed to update password";
            }
        }
        
        header('Location: index.php?page=settings');
        exit;
    }
}
?>