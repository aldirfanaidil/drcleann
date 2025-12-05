<?php
require_once __DIR__ . '/core/init.php';

$customer = new CustomerController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $customer->index();
        break;
        
    case 'create_order':
        $customer->createOrder();
        break;
        
    case 'thank_you':
        $customer->thankYou();
        break;
        
    default:
        $customer->index();
        break;
}
?>