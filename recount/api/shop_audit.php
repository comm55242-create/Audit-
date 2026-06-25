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

$item_code1 = isset($_POST['item_code']) ? $_POST['item_code'] : '';

$query = "SELECT item_code,item_name,barcode,price,dept,shop_code,curr_stock, vc_unit as UNIT 
          FROM master_item 
          WHERE item_code=:item_code OR barcode=:item_code";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':item_code', $item_code1);
oci_execute($stmt);

$results = [];
while ($row = oci_fetch_assoc($stmt)) {
    $results[] = $row;
}
header('Content-Type: application/json');
echo json_encode($results);
?>
