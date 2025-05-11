<?php
require_once 'models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process login form
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Validate input
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Email and password are required";
                require_once 'views/auth/login.php';
                return;
            }
            
            // Attempt to login
            $user = $this->userModel->getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['theme'] = $user['theme'];
                $_SESSION['currency'] = $user['currency'];
                
                // Redirect to dashboard
                header('Location: index.php?page=dashboard');
                exit;
            } else {
                // Login failed
                $_SESSION['error'] = "Invalid email or password";
                require_once 'views/auth/login.php';
                return;
            }
        } else {
            // Display login form
            require_once 'views/auth/login.php';
        }
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process registration form
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $firstName = $_POST['first_name'] ?? '';
            $lastName = $_POST['last_name'] ?? '';
            $currency = $_POST['currency'] ?? DEFAULT_CURRENCY;
            
            // Validate input
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                $_SESSION['error'] = "All fields are required";
                require_once 'views/auth/register.php';
                return;
            }
            
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = "Passwords do not match";
                require_once 'views/auth/register.php';
                return;
            }
            
            // Check if email already exists
            if ($this->userModel->getUserByEmail($email)) {
                $_SESSION['error'] = "Email already in use";
                require_once 'views/auth/register.php';
                return;
            }
            
            // Check if username already exists
            if ($this->userModel->getUserByUsername($username)) {
                $_SESSION['error'] = "Username already in use";
                require_once 'views/auth/register.php';
                return;
            }
            
            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userId = $this->userModel->createUser($username, $email, $hashedPassword, $firstName, $lastName, $currency);
            
            if ($userId) {
                // Registration successful
                $_SESSION['success'] = "Registration successful. Please login.";
                header('Location: index.php?page=login');
                exit;
            } else {
                // Registration failed
                $_SESSION['error'] = "Registration failed. Please try again.";
                require_once 'views/auth/register.php';
                return;
            }
        } else {
            // Display registration form
            require_once 'views/auth/register.php';
        }
    }
    
    public function logout() {
        // Destroy session
        session_destroy();
        
        // Redirect to login page
        header('Location: index.php?page=login');
        exit;
    }
    
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['email'])) {
                // Step 1: Request password reset
                $email = $_POST['email'];
                
                // Check if email exists
                $user = $this->userModel->getUserByEmail($email);
                
                if (!$user) {
                    $_SESSION['error'] = "Email not found";
                    require_once 'views/auth/reset_password.php';
                    return;
                }
                
                // Generate token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Save token
                $this->userModel->createPasswordResetToken($user['id'], $token, $expires);
                
                // In a real application, send email with reset link
                // For this demo, we'll just show the token
                $_SESSION['success'] = "Password reset link has been sent to your email. Token: " . $token;
                require_once 'views/auth/reset_password.php';
                return;
            } elseif (isset($_POST['token'], $_POST['password'], $_POST['confirm_password'])) {
                // Step 2: Reset password
                $token = $_POST['token'];
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];
                
                // Validate passwords
                if ($password !== $confirmPassword) {
                    $_SESSION['error'] = "Passwords do not match";
                    require_once 'views/auth/reset_password_confirm.php';
                    return;
                }
                
                // Verify token
                $tokenData = $this->userModel->getPasswordResetToken($token);
                
                if (!$tokenData || strtotime($tokenData['expires_at']) < time()) {
                    $_SESSION['error'] = "Invalid or expired token";
                    require_once 'views/auth/reset_password.php';
                    return;
                }
                
                // Update password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $this->userModel->updatePassword($tokenData['user_id'], $hashedPassword);
                
                // Delete token
                $this->userModel->deletePasswordResetToken($token);
                
                $_SESSION['success'] = "Password has been reset successfully. Please login.";
                header('Location: index.php?page=login');
                exit;
            }
        } elseif (isset($_GET['token'])) {
            // Display password reset confirmation form
            $token = $_GET['token'];
            require_once 'views/auth/reset_password_confirm.php';
        } else {
            // Display password reset request form
            require_once 'views/auth/reset_password.php';
        }
    }
    
    public function updateProfile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Process profile update form
            $userId = $_SESSION['user_id'];
            $firstName = $_POST['first_name'] ?? '';
            $lastName = $_POST['last_name'] ?? '';
            $currency = $_POST['currency'] ?? DEFAULT_CURRENCY;
            $theme = $_POST['theme'] ?? 'light';
            
            // Update user profile
            $success = $this->userModel->updateProfile($userId, $firstName, $lastName, $currency, $theme);
            
            if ($success) {
                // Update session variables
                $_SESSION['currency'] = $currency;
                $_SESSION['theme'] = $theme;
                
                $_SESSION['success'] = "Profile updated successfully";
            } else {
                $_SESSION['error'] = "Failed to update profile";
            }
            
            // Redirect back to settings page
            header('Location: index.php?page=settings');
            exit;
        } else {
            // Redirect to settings page
            header('Location: index.php?page=settings');
            exit;
        }
    }
}
?>