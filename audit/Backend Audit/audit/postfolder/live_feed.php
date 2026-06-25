<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['storecode'])) {
    echo json_encode(['error' => 'No session storecode found.']);
    exit;
}

$SHOP_CODE = $_SESSION['storecode'];

$conn = oci_connect('SHOP', 'SHOP', '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Fetch the 5 most recent scans for this store
$sql = oci_parse($conn, "
    SELECT * FROM (
        SELECT a.ITEM_CODE, a.QTY, a.DATE_SYS, a.EMP_CODE, m.ITEM_NAME, m.BARCODE, NVL(m.VC_UNIT, 'PCS') as VC_UNIT, u.E_NAME 
        FROM HEAD_AUDIT a 
        LEFT JOIN MASTER_ITEM m ON a.ITEM_CODE = m.ITEM_CODE 
        LEFT JOIN USERS u ON a.EMP_CODE = u.STAFF_ID 
        WHERE a.SHOP_CODE = '{$SHOP_CODE}' 
        ORDER BY a.DATE_SYS DESC
    ) WHERE ROWNUM <= 5
");

if (!$sql) {
    echo json_encode(['error' => 'Query failed.']);
    exit;
}

$result = oci_execute($sql);
if (!$result) {
    echo json_encode(['error' => 'Execution failed.']);
    exit;
}

$items = [];
while ($row = oci_fetch_assoc($sql)) {
    // Return all data required for the UI
    $items[] = [
        'ITEM_CODE' => $row['ITEM_CODE'],
        'ITEM_NAME' => isset($row['ITEM_NAME']) ? $row['ITEM_NAME'] : 'Unknown Item',
        'BARCODE' => isset($row['BARCODE']) ? $row['BARCODE'] : 'N/A',
        'QTY' => $row['QTY'],
        'DATE_SYS' => $row['DATE_SYS'],
        'VC_UNIT' => $row['VC_UNIT'],
        'E_NAME' => isset($row['E_NAME']) ? $row['E_NAME'] : 'Unknown User'
    ];
}

echo json_encode($items);
?>
