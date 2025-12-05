<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->user = new User($db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => Security::sanitizeInput($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? ''
            ];

            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('./login.php');
            }

            // Validate input
            $errors = [];
            if (empty($data['username'])) {
                $errors['username'] = 'Username is required';
            }
            if (empty($data['password'])) {
                $errors['password'] = 'Password is required';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                redirect('./login.php');
            }

            // Attempt login
            $result = $this->user->login($data['username'], $data['password']);

            if ($result['success']) {
                // Set session
                $_SESSION['user_id'] = $result['user']['id'];
                $_SESSION['username'] = $result['user']['username'];
                $_SESSION['user_role'] = $result['user']['role'];
                $_SESSION['full_name'] = $result['user']['full_name'];
                $_SESSION['email'] = $result['user']['email'];
                $_SESSION['csrf_token'] = Security::generateCSRFToken();

                // Debug logging
                error_log("LOGIN SUCCESS - User: " . $result['user']['username'] . " Role: " . $result['user']['role']);
                error_log("Session data: " . json_encode($_SESSION));

                // Log activity
                Security::logActivity($result['user']['id'], 'login', 'User logged in');

                // Redirect based on role
                $role = $_SESSION['user_role'];
                if ($role === 'admin' || $role === 'superuser') {
                    error_log("Redirecting admin/superuser to: admin/index.php?page=dashboard");
                    redirect('./admin/index.php?page=dashboard');
                } elseif ($role === 'kasir') {
                    error_log("Redirecting kasir to: admin/index.php?page=orders");
                    redirect('./admin/index.php?page=orders');
                } else {
                    error_log("Redirecting other roles to: index.php");
                    redirect('./index.php');
                }
            } else {
                error_log("LOGIN FAILED - User: " . $data['username'] . " Reason: " . $result['message']);
                $_SESSION['error'] = $result['message'];
                $_SESSION['old'] = $data;
                redirect('./login.php');
            }
        }
    }

    public function logout() {
        if (isLoggedIn()) {
            Security::logActivity($_SESSION['user_id'], 'logout', 'User logged out');
        }

        // Destroy session
        session_destroy();
        
        // Clear session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        redirect('./login.php');
    }

    public function showLoginForm() {
        // If already logged in, redirect to dashboard
        if (isLoggedIn()) {
            redirect('./admin/index.php?page=dashboard');
        }

        include __DIR__ . '/../views/auth/login.php';
    }

    public function showForgotPassword() {
        include __DIR__ . '/../views/auth/forgot_password.php';
    }

    public function processForgotPassword() {
        // Implementation for password reset
        $_SESSION['info'] = 'Password reset functionality will be implemented.';
        redirect('./login.php');
    }

    public function changePassword() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate CSRF token
            if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
                $_SESSION['error'] = 'Invalid request. Please try again.';
                redirect('./admin/index.php?page=change_password');
            }

            $errors = [];

            // Validate current password by attempting login
            $loginResult = $this->user->login($_SESSION['username'], $currentPassword);
            if (!$loginResult['success']) {
                $errors['current_password'] = 'Current password is incorrect';
            }

            // Validate new password
            if (strlen($newPassword) < 8) {
                $errors['new_password'] = 'Password must be at least 8 characters long';
            }

            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = 'Passwords do not match';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                redirect('./admin/index.php?page=change_password');
            }

            // Update password
            if ($this->user->updatePassword($_SESSION['user_id'], $newPassword)) {
                $_SESSION['success'] = 'Password changed successfully';
                Security::logActivity($_SESSION['user_id'], 'password_changed', 'User changed password');
            } else {
                $_SESSION['error'] = 'Failed to change password';
            }

            redirect('./admin/index.php?page=dashboard');
        }
    }

    public function showChangePasswordForm() {
        requireLogin();
        include __DIR__ . '/../views/admin/change_password.php';
    }
}
?>