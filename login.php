<?php
require_once __DIR__ . '/core/init.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $auth->login();
        } else {
            $auth->showLoginForm();
        }
        break;
        
    case 'logout':
        $auth->logout();
        break;
        
    case 'forgot':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->processForgotPassword();
        } else {
            $auth->showForgotPassword();
        }
        break;
        
    case 'change_password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->changePassword();
        } else {
            $auth->showChangePasswordForm();
        }
        break;
        
    default:
        $auth->showLoginForm();
        break;
}
?>