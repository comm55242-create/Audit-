<?php
session_start();
require_once('includes/connection.php');

if (!isset($_SESSION['storecode'])) {
    echo json_encode([]);
    exit;
}

$shop_code = $_SESSION['storecode'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'list';

if ($action == 'list') {
    $query = "SELECT ID, USER_NAME, ZONE_NAME, STATUS, TO_CHAR(REQUEST_TIME, 'YYYY-MM-DD HH24:MI:SS') AS REQUEST_TIME 
              FROM RECOUNT_APPROVALS 
              WHERE STATUS = 'PENDING' 
              ORDER BY REQUEST_TIME DESC";
    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);
    
    $results = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $results[] = $row;
    }
    echo json_encode($results);
} 
elseif ($action == 'approve' || $action == 'reject') {
    $id = isset($_POST['id']) ? $_POST['id'] : 0;
    $status = ($action == 'approve') ? 'APPROVED' : 'REJECTED';
    
    if ($id > 0) {
        $query = "UPDATE RECOUNT_APPROVALS SET STATUS = :status WHERE ID = :id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':status', $status);
        oci_bind_by_name($stmt, ':id', $id);
        
        if(oci_execute($stmt, OCI_COMMIT_ON_SUCCESS)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}
?>
