<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - DASHBOARD CONTROLLER
// Validates session authenticity and renders the main tabbed corporate dashboard.
// ==========================================================================

class DashboardController {
    
    /**
     * Enforces active auditor authentication guard.
     */
    public function __construct() {
        if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    /**
     * Renders the tabbed corporate dashboard.
     */
    public function show() {
        require_once __DIR__ . '/../views/dashboard/main.php';
    }
}
?>
