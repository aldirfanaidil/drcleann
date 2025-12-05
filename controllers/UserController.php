<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $user;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->user = new User($db);
    }

    public function index() {
        requireSuperuser();
        
        $users = $this->user->getAll();
        include __DIR__ . '/../views/admin/users/index.php';
    }

    public function create() {
        requireSuperuser();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->store();
        } else {
            include __DIR__ . '/../views/admin/users/create.php';
        }
    }

    public function edit() {
        requireSuperuser();
        
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['error'] = 'Invalid user ID';
            redirect('?page=users');
        }

        $user = $this->user->getById($id);
        if (!$user) {
            $_SESSION['error'] = 'User not found';
            redirect('?page=users');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
        } else {
            include __DIR__ . '/../views/admin/users/edit.php';
        }
    }

    public function delete() {
        requireSuperuser();
        
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
            return;
        }

        // Prevent self-deletion
        if ($id === $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
            return;
        }

        // Validate CSRF token
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $result = $this->user->delete($id);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
        }
    }

    private function store() {
        // Validate CSRF token
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request. Please try again.';
            redirect('?page=user_create');
        }

        $data = [
            'username' => Security::sanitizeInput($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'kasir',
            'full_name' => Security::sanitizeInput($_POST['full_name'] ?? ''),
            'email' => Security::sanitizeInput($_POST['email'] ?? ''),
            'phone' => Security::sanitizeInput($_POST['phone'] ?? '')
        ];

        // Validate input
        $errors = $this->validateUserData($data, true);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect('?page=user_create');
        }

        // Check if username already exists
        if ($this->user->usernameExists($data['username'])) {
            $_SESSION['errors']['username'] = 'Username already exists';
            $_SESSION['old'] = $data;
            redirect('?page=user_create');
        }

        // Check if email already exists
        if ($this->user->emailExists($data['email'])) {
            $_SESSION['errors']['email'] = 'Email already exists';
            $_SESSION['old'] = $data;
            redirect('?page=user_create');
        }

        // Create user
        $userId = $this->user->create($data);

        if ($userId) {
            $_SESSION['success'] = 'User created successfully';
            redirect('?page=users');
        } else {
            $_SESSION['error'] = 'Failed to create user';
            $_SESSION['old'] = $data;
            redirect('?page=user_create');
        }
    }

    private function update($id) {
        // Validate CSRF token
        if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid request. Please try again.';
            redirect("?page=user_edit&id=$id");
        }

        $data = [
            'username' => Security::sanitizeInput($_POST['username'] ?? ''),
            'role' => $_POST['role'] ?? 'kasir',
            'full_name' => Security::sanitizeInput($_POST['full_name'] ?? ''),
            'email' => Security::sanitizeInput($_POST['email'] ?? ''),
            'phone' => Security::sanitizeInput($_POST['phone'] ?? '')
        ];

        // Validate input
        $errors = $this->validateUserData($data, false);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            redirect("?page=user_edit&id=$id");
        }

        // Check if username already exists (excluding current user)
        if ($this->user->usernameExists($data['username'], $id)) {
            $_SESSION['errors']['username'] = 'Username already exists';
            $_SESSION['old'] = $data;
            redirect("?page=user_edit&id=$id");
        }

        // Check if email already exists (excluding current user)
        if ($this->user->emailExists($data['email'], $id)) {
            $_SESSION['errors']['email'] = 'Email already exists';
            $_SESSION['old'] = $data;
            redirect("?page=user_edit&id=$id");
        }

        // Update user
        if ($this->user->update($id, $data)) {
            $_SESSION['success'] = 'User updated successfully';
            redirect('?page=users');
        } else {
            $_SESSION['error'] = 'Failed to update user';
            $_SESSION['old'] = $data;
            redirect("?page=user_edit&id=$id");
        }
    }

    private function validateUserData($data, $isNew = false) {
        $errors = [];

        // Username validation
        if (empty($data['username'])) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters';
        } elseif (strlen($data['username']) > 50) {
            $errors['username'] = 'Username must not exceed 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores';
        }

        // Password validation (only for new users)
        if ($isNew) {
            if (empty($data['password'])) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($data['password']) < 8) {
                $errors['password'] = 'Password must be at least 8 characters';
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $data['password'])) {
                $errors['password'] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
            }
        }

        // Role validation
        if (!in_array($data['role'], ['superuser', 'admin', 'kasir'])) {
            $errors['role'] = 'Invalid role';
        }

        // Full name validation
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        } elseif (strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Full name must be at least 2 characters';
        } elseif (strlen($data['full_name']) > 100) {
            $errors['full_name'] = 'Full name must not exceed 100 characters';
        }

        // Email validation
        if (!empty($data['email'])) {
            if (!Security::validateEmail($data['email'])) {
                $errors['email'] = 'Invalid email format';
            }
        }

        // Phone validation
        if (!empty($data['phone'])) {
            if (!Security::validatePhone(preg_replace('/[^0-9]/', '', $data['phone']))) {
                $errors['phone'] = 'Invalid phone number format';
            }
        }

        return $errors;
    }
}
?>