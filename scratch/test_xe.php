<?php
$conn = oci_connect('SHOP', 'SHOP', '(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = xe)
    )
  )');

if (!$conn) {
    $e = oci_error();
    echo "Connection failed: " . $e['message'] . "\n";
    exit;
}

echo "Connection successful!\n";
$query = "SELECT column_name, data_type FROM user_tab_cols WHERE UPPER(table_name) = 'MST_DEPT' ORDER BY column_id";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
    echo $row['COLUMN_NAME'] . " (" . $row['DATA_TYPE'] . ")\n";
}

oci_free_statement($stmt);
oci_close($conn);
?>
