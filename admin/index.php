<?php
define('ADMIN_CONTROLLER', true);
require_once __DIR__ . '/../core/init.php';

// Debug session
error_log("ADMIN INDEX - Session ID: " . session_id());
error_log("ADMIN INDEX - User ID: " . ($_SESSION['user_id'] ?? 'not set'));
error_log("ADMIN INDEX - User Role: " . ($_SESSION['user_role'] ?? 'not set'));

// Check if user is logged in
if (!isLoggedIn()) {
    error_log("ADMIN INDEX - User not logged in, redirecting to login");
    redirect('../login.php');
}

$page = $_GET['page'] ?? 'dashboard';

// Route to appropriate controller
switch ($page) {
    case 'dashboard':
        requireAdmin();
        $dashboard = new DashboardController();
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'index':
                $dashboard->index();
                break;
            case 'getChartData':
                $dashboard->getChartData();
                break;
            case 'getKpiData':
                $dashboard->getKpiData();
                break;
            case 'getDetailData':
                $dashboard->getDetailData();
                break;
            case 'getSimpleDetailData':
                $dashboard->getSimpleDetailData();
                break;
            default:
                $dashboard->index();
                break;
        }
        break;

    case 'orders':
        requireAdmin();
        $orderController = new OrderController();
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'index':
                $orderController->index();
                break;
            case 'getReportData':
                $orderController->getReportData();
                break;
            case 'checkNewOrders':
                $orderController->checkNewOrders();
                break;
            default:
                $orderController->index();
                break;
        }
        break;

    case 'order_detail':
        requireAdmin();
        $orderController = new OrderController();
        $orderController->detail();
        break;

    case 'add_order':
        requireAdmin();
        $orderController = new OrderController();
        $orderController->create();
        break;

    case 'order_edit':
        requireAdmin();
        $orderController = new OrderController();
        $orderController->edit();
        break;

    case 'print_invoice':
        requireAdmin();
        $orderController = new OrderController();
        $orderController->printInvoice();
        break;

    case 'order_delete':
        requireSuperuser();
        $orderController = new OrderController();
        $orderController->delete();
        break;

    case 'reports':
        requireAdmin();
        include __DIR__ . '/../views/admin/reports/index.php';
        break;

    case 'cash_flow':
        requireSuperuser();
        $cashFlowController = new CashFlowController();
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'index':
                $cashFlowController->index();
                break;
            case 'getChartData':
                $cashFlowController->getCashFlowChartData();
                break;
            case 'expenses':
                $cashFlowController->expenses();
                break;
            case 'add_expense':
                $cashFlowController->addExpense();
                break;
            case 'edit_expense':
                $id = $_GET['id'] ?? 0;
                $cashFlowController->editExpense($id);
                break;
            case 'delete_expense':
                $id = $_GET['id'] ?? 0;
                $cashFlowController->deleteExpense($id);
                break;
            default:
                $cashFlowController->index();
                break;
        }
        break;

    case 'users':
        requireSuperuser();
        $userController = new UserController();
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'index':
                $userController->index();
                break;
            case 'create':
                $userController->create();
                break;
            case 'edit':
                $userController->edit();
                break;
            default:
                $userController->index();
                break;
        }
        break;

    case 'user_create':
        requireSuperuser();
        $userController = new UserController();
        $userController->create();
        break;

    case 'user_edit':
        requireSuperuser();
        $userController = new UserController();
        $userController->edit();
        break;

    case 'user_delete':
        requireSuperuser();
        $userController = new UserController();
        $userController->delete();
        break;



    case 'settings':
        requireAdmin();
        $settingsController = new SettingsController();
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'index':
                include __DIR__ . '/../views/admin/settings/index.php';
                break;
            case 'saveGeneral':
                $settingsController->saveGeneral();
                break;
            case 'savePayment':
                $settingsController->savePayment();
                break;
            case 'saveServices':
                $settingsController->saveServices();
                break;
            case 'backup':
                $settingsController->backup();
                break;
            case 'clearCache':
                $settingsController->clearCache();
                break;
            case 'reset':
                $settingsController->reset();
                break;
            default:
                include __DIR__ . '/../views/admin/settings/index.php';
                break;
        }
        break;

    case 'change_password':
        $auth = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->changePassword();
        } else {
            $auth->showChangePasswordForm();
        }
        break;

    case 'logout':
        $auth = new AuthController();
        $auth->logout();
        break;

    default:
        requireAdmin();
        $dashboard = new DashboardController();
        $dashboard->index();
        break;
}
?>