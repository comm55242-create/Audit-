<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - MASTER ROUTER ENTRY POINT
// Initializes core auditor sessions and routes URL routes to MVC controllers.
// ==========================================================================

session_start();

$route = isset($_GET['route']) ? trim($_GET['route']) : 'dashboard';

// Dynamic MVC Controllers loader
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AuditController.php';
require_once __DIR__ . '/controllers/DashboardController.php';

try {
    switch ($route) {
        case 'login':
            $controller = new AuthController();
            $controller->showLogin();
            break;
            
        case 'login/submit':
            $controller = new AuthController();
            $controller->handleLogin();
            break;

        case 'otp':
            $controller = new AuthController();
            $controller->showOtp();
            break;

        case 'otp/verify':
            $controller = new AuthController();
            $controller->handleVerify();
            break;

        case 'otp/resend':
            $controller = new AuthController();
            $controller->handleResend();
            break;

        case 'logout':
            $controller = new AuthController();
            $controller->handleLogout();
            break;

        case 'dashboard':
            $controller = new DashboardController();
            $controller->show();
            break;

        case 'audit/search':
            $controller = new AuditController();
            $controller->handleSearch();
            break;

        case 'audit/lookup':
            $controller = new AuditController();
            $controller->handleLookup();
            break;

        case 'audit/shop-lookup':
            $controller = new AuditController();
            $controller->handleShopLookup();
            break;

        case 'audit/save':
            $controller = new AuditController();
            $controller->handleSave();
            break;

        case 'audit/preview-items':
            $controller = new AuditController();
            $controller->handlePreviewItems();
            break;

        case 'audit/summary':
            $controller = new AuditController();
            $controller->handleGetSummary();
            break;


        default:
            $controller = new DashboardController();
            $controller->show();
            break;
    }
} catch (Exception $e) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Melcom System Router Exception: " . $e->getMessage();
}
?>
