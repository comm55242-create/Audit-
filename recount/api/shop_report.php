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

$username = isset($_POST['username']) ? $_POST['username'] : '';
$rack_num = isset($_POST['rack_num']) ? $_POST['rack_num'] : '';

// Clean up the username and rack_num since the app passes "Audit Name: HS" and "Zone: Zone A"
$username = str_replace("Audit Name: ", "", $username);
$rack_num = str_replace("Zone: ", "", $rack_num);

// The apk sets rack_num as 'Recount_M' in the db, so we might want to filter by that instead
$rack_filter = 'Recount_M';

// Fetch the recount report from HEAD_AUDIT joined with MASTER_ITEM
$query = "SELECT h.item_code as ITEM_CODE, m.item_name as ITEM_NAME, SUM(h.qty) as QTY 
          FROM head_audit h
          LEFT JOIN master_item m ON h.item_code = m.item_code
          WHERE h.user_name = :username AND h.rack_num = :rack_num AND h.audit_round = 2
          GROUP BY h.item_code, m.item_name";

$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':username', $username);
oci_bind_by_name($stmt, ':rack_num', $rack_filter);
oci_execute($stmt);

$results = [];
while ($row = oci_fetch_assoc($stmt)) {
    // The Input_values_Vview.java expects uppercase keys: ITEM_CODE, ITEM_NAME, QTY
    $results[] = $row;
}

header('Content-Type: application/json');
echo json_encode($results);
?>
