<?php
$possible_paths = [
    '../../admin_audit/includes/connection.php',
    '../../includes/connection.php',
    '../../../includes/connection.php',
    '../../../co/reports/admin_audit/includes/connection.php',
    '../../co/reports/admin_audit/includes/connection.php',
    '../includes/connection.php',
    'includes/connection.php'
];
$conn_found = false;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $conn_found = true;
        break;
    }
}
if (!$conn_found) {
    echo json_encode(['status' => 'ERROR', 'message' => 'Database connection file not found']);
    exit;
}

$shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : '';
$item_code = isset($_POST['item_code']) ? $_POST['item_code'] : '';
$qty       = isset($_POST['qty']) ? (float)$_POST['qty'] : 0;
$user      = isset($_POST['user']) ? $_POST['user'] : '';
$ip        = isset($_POST['ip']) ? $_POST['ip'] : '';
$rack      = 'Recount_M'; // Force rack to Recount_M for mobile recounting

if (empty($shop_code) || empty($item_code)) {
    echo "Error";
    exit;
}

// Differential Logic
$audit_qty_query = "SELECT SUM(QTY) AS EXISTING_QTY FROM HEAD_AUDIT WHERE ITEM_CODE = :item_code AND SHOP_CODE = :shop_code AND QTY <> 0";
$stmt1 = oci_parse($conn, $audit_qty_query);
oci_bind_by_name($stmt1, ':item_code', $item_code);
oci_bind_by_name($stmt1, ':shop_code', $shop_code);
oci_execute($stmt1);
$row = oci_fetch_assoc($stmt1);
$existing_qty = $row ? (float)$row['EXISTING_QTY'] : 0;

$qty_to_insert = $qty - $existing_qty;

if ($qty_to_insert != 0) {
    $dtime = date('Y-m-d H:i:s');
    $audit_round = 2;

    $insert = "INSERT INTO head_audit (shop_code, item_code, qty, date_sys, emp_code, IP, user_name, rack_num, audit_round) 
               VALUES (:shop_code, :item_code, :qty, TO_DATE(:dtime, 'YYYY-MM-DD HH24:MI:SS'), :emp_code, :ip, :user_name, :rack, :audit_round)";
    $stmt2 = oci_parse($conn, $insert);
    oci_bind_by_name($stmt2, ':shop_code', $shop_code);
    oci_bind_by_name($stmt2, ':item_code', $item_code);
    oci_bind_by_name($stmt2, ':qty', $qty_to_insert);
    oci_bind_by_name($stmt2, ':dtime', $dtime);
    oci_bind_by_name($stmt2, ':emp_code', $user);
    oci_bind_by_name($stmt2, ':ip', $ip);
    oci_bind_by_name($stmt2, ':user_name', $user);
    oci_bind_by_name($stmt2, ':rack', $rack);
    oci_bind_by_name($stmt2, ':audit_round', $audit_round);
    
    if (oci_execute($stmt2, OCI_COMMIT_ON_SUCCESS)) {
        echo "Sucess";
    } else {
        echo "Error";
    }
} else {
    // If diff is 0, we do nothing but tell the app it succeeded
    echo "Sucess";
}
?>
