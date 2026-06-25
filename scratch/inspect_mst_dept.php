<?php
$conn = oci_connect('SHOP', 'SHOP', '(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = orcl)
    )
  )');

if (!$conn) {
    echo "Connection failed!";
    exit;
}

echo "Inspecting MST_DEPT columns:\n";
$query = "SELECT column_name, data_type, data_length FROM user_tab_cols WHERE UPPER(table_name) = 'MST_DEPT' ORDER BY column_id";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);

while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
    echo $row['COLUMN_NAME'] . " (" . $row['DATA_TYPE'] . " - " . $row['DATA_LENGTH'] . ")\n";
}

oci_free_statement($stmt);
oci_close($conn);
?>
