<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - AUDIT CONTROLLER
// Validates session authenticity and routes AJAX item searches, batch lookups, and setup saves.
// ==========================================================================

require_once __DIR__ . '/../models/AuditModel.php';

class AuditController {
    
    /**
     * Enforces active session protection across all AJAX actions.
     */
    public function __construct() {
        if (!isset($_SESSION['user_authenticated']) || $_SESSION['user_authenticated'] !== true) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'Session expired or invalid authorization. Please log in again.']);
            exit;
        }
    }

    /**
     * Resolves real-time character keyword searches.
     */
    public function handleSearch() {
        header('Content-Type: application/json; charset=utf-8');
        $query = isset($_GET['query']) ? trim($_GET['query']) : '';
        $results = AuditModel::searchItems($query);
        echo json_encode($results);
        exit;
    }

    /**
     * Resolves fast parameter-bound lookups for CSV item configurations.
     */
    public function handleLookup() {
        header('Content-Type: application/json; charset=utf-8');
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        $codes = isset($data['codes']) ? $data['codes'] : [];
        $results = AuditModel::lookupBatchItems($codes);
        echo json_encode($results);
    }

    /**
     * Resolves dynamic shop code queries from remote MST_SHOP table.
     */
    public function handleShopLookup() {
        header('Content-Type: application/json; charset=utf-8');
        $shop_code = isset($_GET['shop_code']) ? trim($_GET['shop_code']) : '';
        $result = AuditModel::lookupShopCode($shop_code);
        echo json_encode($result);
        exit;
    }

    /**
     * Processes OCI transactional parameter saves and triggers auto DDL alterations.
     */
    public function handleSave() {
        header('Content-Type: text/plain; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Invalid request method.";
            exit;
        }

        try {
            $params = [
                'shop_code' => isset($_POST['shop_code']) ? $_POST['shop_code'] : '',
                'stock_date' => isset($_POST['stock_date']) ? $_POST['stock_date'] : '',
                'audit_type' => isset($_POST['audit_type']) ? $_POST['audit_type'] : '',
                'audit_mode' => isset($_POST['audit_mode']) ? $_POST['audit_mode'] : '',
                'depts' => isset($_POST['depts']) ? $_POST['depts'] : '',
                'groups' => isset($_POST['groups']) ? $_POST['groups'] : '',
                'subgroups' => isset($_POST['subgroups']) ? $_POST['subgroups'] : '',
                'mail' => isset($_SESSION['logged_in_email']) ? $_SESSION['logged_in_email'] : ''
            ];

            $res = AuditModel::saveSetupConfiguration($params);
            echo $res;
            exit;
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
?>
