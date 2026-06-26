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
        // ── STREAMING SETUP ───────────────────────────────────────────────────
        // 1. Release PHP session write lock — without this, Apache holds output
        //    until the session lock is released (i.e. request end). Critical.
        session_write_close();

        // 2. Disable all compression and buffering
        header('Content-Type: text/plain; charset=utf-8');
        header('X-Accel-Buffering: no');       // Nginx: disable proxy buffering
        header('Content-Encoding: none');       // Disable gzip — compressed output cannot stream
        header('Cache-Control: no-cache');
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);
        while (ob_get_level() > 0) { ob_end_clean(); }

        // 3. Send > 8 KB of whitespace padding so Apache's output buffer
        //    threshold is immediately exceeded and streaming begins.
        //    The frontend ignores non-JSON lines.
        echo str_repeat(' ', 8193) . "\n";
        flush();
        // ─────────────────────────────────────────────────────────────────────

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Invalid request method.";
            exit;
        }

        try {
            $params = [
                'shop_code'  => isset($_POST['shop_code'])  ? $_POST['shop_code']  : '',
                'stock_date' => isset($_POST['stock_date']) ? $_POST['stock_date'] : '',
                'audit_type' => isset($_POST['audit_type']) ? $_POST['audit_type'] : '',
                'audit_mode' => isset($_POST['audit_mode']) ? $_POST['audit_mode'] : '',
                'depts'      => isset($_POST['depts'])      ? $_POST['depts']      : '',
                'groups'     => isset($_POST['groups'])     ? $_POST['groups']     : '',
                'subgroups'  => isset($_POST['subgroups'])  ? $_POST['subgroups']  : '',
                'mail'       => isset($_SESSION['logged_in_email']) ? $_SESSION['logged_in_email'] : ''
            ];

            $res = AuditModel::saveSetupConfiguration($params);
            echo $res;
            exit;
        } catch (Exception $e) {
            echo "ERROR:" . $e->getMessage();
            exit;
        }
    }

    /**
     * Handles retrieving scoped items for dynamic Step 8 summary/preview.
     */
    public function handlePreviewItems() {
        header('Content-Type: application/json; charset=utf-8');
        // Accept POST payload to avoid URI Too Long errors with many subgroups
        $params = [
            'audit_type' => isset($_POST['audit_type']) ? $_POST['audit_type'] : '',
            'depts' => isset($_POST['depts']) ? $_POST['depts'] : '',
            'groups' => isset($_POST['groups']) ? $_POST['groups'] : '',
            'subgroups' => isset($_POST['subgroups']) ? $_POST['subgroups'] : '',
            'shop_code' => isset($_POST['shop_code']) ? $_POST['shop_code'] : ''
        ];
        try {
            $results = AuditModel::getScopedItemsPreview($params);
            echo json_encode($results);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Retrieves the sync summary stats from MASTER_ITEM.
     */
    public function handleGetSummary() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $summary = AuditModel::getSyncSummary();
            echo json_encode($summary);
            exit;
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
    /**
     * Bridges session variables to the Old System before redirecting.
     */
    public function handleBridge() {
        $shopCode = isset($_GET['shop']) ? $_GET['shop'] : '';
        
        // Inject old system session variables
        $_SESSION['storecode'] = $shopCode;
        $_SESSION['storename'] = $shopCode; // Old system just needs this set to pass confirm_logged_in()
        $_SESSION['username'] = 'ADMIN';
        $_SESSION['staff_id'] = 'ADMIN';
        $_SESSION['rack_number'] = ""; // Match old system login behavior
        
        try {
            AuditModel::initializeStockTakeSession($shopCode, 'ADMIN');
        } catch (Exception $e) {
            // If it fails, log or handle the error (we could redirect to an error page, but for now we'll just let the bridge continue so they aren't fully stuck)
            error_log("Stock Take Initialization Failed: " . $e->getMessage());
        }
        
        // Redirect to the old system index (relative so it works inside subfolders like Audit_new)
        header('Location: admin_audit/index.php');
        exit;
    }
}
?>
