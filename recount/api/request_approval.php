<?php
$log_file = dirname(__FILE__) . '/recount_log.txt';
function write_log($msg) {
    global $log_file;
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $msg . "\n", FILE_APPEND);
}

write_log("Request received. POST data: " . json_encode($_POST));

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
        write_log("Found connection.php at: " . $path);
        $conn_found = true;
        break;
    }
}
if (!$conn_found) {
    write_log("ERROR: Database connection file not found");
    echo json_encode(['status' => 'ERROR', 'message' => 'Database connection file not found']);
    exit;
}

$shop_code = isset($_POST['shop_code']) && !empty($_POST['shop_code']) ? $_POST['shop_code'] : '0';
$user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
$zone_name = isset($_POST['zone_name']) ? $_POST['zone_name'] : '';

write_log("Variables parsed - Shop: $shop_code, User: $user_name, Zone: $zone_name");

if (empty($user_name) || empty($zone_name)) {
    write_log("ERROR: user_name or zone_name is empty. Returning ERROR status.");
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
$exec = oci_execute($stmt);

if (!$exec) {
    $e = oci_error($stmt);
    write_log("SQL Error on SELECT: " . $e['message']);
}

if ($row = oci_fetch_assoc($stmt)) {
    write_log("Found existing request with status: " . $row['STATUS']);
    echo json_encode(['status' => $row['STATUS']]);
} else {
    write_log("No existing request found, inserting new PENDING request.");
    // Insert new pending request
    $insert = "INSERT INTO RECOUNT_APPROVALS (SHOP_CODE, USER_NAME, ZONE_NAME, STATUS) 
               VALUES (:shop_code, :user_name, :zone_name, 'PENDING')";
    $stmt2 = oci_parse($conn, $insert);
    oci_bind_by_name($stmt2, ':shop_code', $shop_code);
    oci_bind_by_name($stmt2, ':user_name', $user_name);
    oci_bind_by_name($stmt2, ':zone_name', $zone_name);
    $exec2 = oci_execute($stmt2, OCI_COMMIT_ON_SUCCESS);
    
    if (!$exec2) {
        $e = oci_error($stmt2);
        write_log("SQL Error on INSERT: " . $e['message']);
    } else {
        write_log("Successfully inserted PENDING request.");
    }
    
    echo json_encode(['status' => 'PENDING']);
}
?>
