<?php
require_once('../../admin_audit/includes/connection.php');

$shop_code = isset($_POST['shop_code']) ? $_POST['shop_code'] : '';
$item_code = isset($_POST['item_code']) ? $_POST['item_code'] : '';
$qty       = isset($_POST['qty']) ? (float)$_POST['qty'] : 0;
$user      = isset($_POST['user']) ? $_POST['user'] : '';
$ip        = isset($_POST['ip']) ? $_POST['ip'] : '';
$rack      = 'Recount_U'; // Force rack to Recount_U for mobile recounting

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
    $insert = "INSERT INTO head_audit (shop_code, item_code, qty, emp_code, IP, user_name, rack_num) 
               VALUES (:shop_code, :item_code, :qty, :emp_code, :ip, :user_name, :rack)";
    $stmt2 = oci_parse($conn, $insert);
    oci_bind_by_name($stmt2, ':shop_code', $shop_code);
    oci_bind_by_name($stmt2, ':item_code', $item_code);
    oci_bind_by_name($stmt2, ':qty', $qty_to_insert);
    oci_bind_by_name($stmt2, ':emp_code', $user);
    oci_bind_by_name($stmt2, ':ip', $ip);
    oci_bind_by_name($stmt2, ':user_name', $user);
    oci_bind_by_name($stmt2, ':rack', $rack);
    
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
