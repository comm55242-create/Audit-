<?php
require_once("includes/connection.php");
require_once("includes/session.php");
confirm_logged_in();

if (isset($_GET['item_code'])) {
    $item_code = trim($_GET['item_code']);
    
    // Support barcode or exact item code, handling leading zeros
    $query = "SELECT ITEM_CODE, ITEM_NAME, PRICE, DEPT, CURR_STOCK, VC_UNIT 
              FROM MASTER_ITEM 
              WHERE ITEM_CODE = :code 
              OR LTRIM(ITEM_CODE, '0') = LTRIM(:code, '0') 
              OR BARCODE = :code";
              
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':code', $item_code);
    oci_execute($stmt);
    
    $row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS);
    
    if ($row) {
        echo json_encode([
            'valid' => true,
            'item_code' => $row['ITEM_CODE'],
            'item_name' => $row['ITEM_NAME'],
            'price' => $row['PRICE'],
            'dept' => $row['DEPT'],
            'stock' => $row['CURR_STOCK'],
            'unit' => isset($row['VC_UNIT']) ? trim($row['VC_UNIT']) : ''
        ]);
    } else {
        echo json_encode(['valid' => false]);
    }
}
?>
