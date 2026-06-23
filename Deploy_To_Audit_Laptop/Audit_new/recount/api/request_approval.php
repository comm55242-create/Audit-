<?php
require_once('../../admin_audit/includes/connection.php');

$shop_code = isset($_POST['shop_code']) && !empty($_POST['shop_code']) ? $_POST['shop_code'] : '0';
$user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
$zone_name = isset($_POST['zone_name']) ? $_POST['zone_name'] : '';

if (empty($user_name) || empty($zone_name)) {
    echo json_encode(['status' => 'ERROR']);
    exit;
}

// Check if there is an existing pending or approved request for this user today
$query = "SELECT STATUS FROM (
            SELECT STATUS FROM RECOUNT_APPROVALS 
            WHERE SHOP_CODE = :shop_code AND USER_NAME = :user_name AND ZONE_NAME = :zone_name 
            AND TRUNC(REQUEST_TIME) = TRUNC(SYSDATE)
            ORDER BY REQUEST_TIME DESC
          ) WHERE ROWNUM = 1";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':shop_code', $shop_code);
oci_bind_by_name($stmt, ':user_name', $user_name);
oci_bind_by_name($stmt, ':zone_name', $zone_name);
oci_execute($stmt);

if ($row = oci_fetch_assoc($stmt)) {
    echo json_encode(['status' => $row['STATUS']]);
} else {
    // Insert new pending request
    $insert = "INSERT INTO RECOUNT_APPROVALS (SHOP_CODE, USER_NAME, ZONE_NAME, STATUS) 
               VALUES (:shop_code, :user_name, :zone_name, 'PENDING')";
    $stmt2 = oci_parse($conn, $insert);
    oci_bind_by_name($stmt2, ':shop_code', $shop_code);
    oci_bind_by_name($stmt2, ':user_name', $user_name);
    oci_bind_by_name($stmt2, ':zone_name', $zone_name);
    oci_execute($stmt2, OCI_COMMIT_ON_SUCCESS);
    
    echo json_encode(['status' => 'PENDING']);
}
?>
