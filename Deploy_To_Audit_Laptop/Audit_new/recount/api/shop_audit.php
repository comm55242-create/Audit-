<?php
require_once('../../admin_audit/includes/connection.php');

$item_code1 = isset($_POST['item_code']) ? $_POST['item_code'] : '';

$query = "SELECT item_code,item_name,barcode,price,dept,shop_code,curr_stock, vc_unit as UNIT 
          FROM master_item 
          WHERE item_code=:item_code OR barcode=:item_code";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':item_code', $item_code1);
oci_execute($stmt);

$results = [];
while ($row = oci_fetch_assoc($stmt)) {
    // Array keys from oci_fetch_assoc are uppercase by default.
    // The Android app expects lowercase keys like in PDO.
    $results[] = array_change_key_case($row, CASE_LOWER);
}
header('Content-Type: application/json');
echo json_encode($results);
?>
