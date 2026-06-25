<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// We try to connect using localhost and service name 'orcl'
$conn = oci_connect('SHOP', 'SHOP', '(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = orcl)
    )
  )');

if (!$conn) {
    // try xe
    $conn = oci_connect('SHOP', 'SHOP', '(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
        (CONNECT_DATA =
          (SERVER = DEDICATED)
          (SERVICE_NAME = xe)
        )
      )');
}

if (!$conn) {
    // try xepdb1
    $conn = oci_connect('SHOP', 'SHOP', '(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
        (CONNECT_DATA =
          (SERVER = DEDICATED)
          (SERVICE_NAME = xepdb1)
        )
      )');
}

if (!$conn) {
    echo "Connection failed!\n";
    exit(1);
}

echo "Connected successfully to local db!\n";

echo "Describing remote view/table VS_ITEM_AUDIT@DB_LINK_SHOP...\n";
$query = "SELECT * FROM VS_ITEM_AUDIT@DB_LINK_SHOP WHERE ROWNUM = 1";
$stmt = oci_parse($conn, $query);
if ($stmt && @oci_execute($stmt)) {
    $ncols = oci_num_fields($stmt);
    for ($i = 1; $i <= $ncols; $i++) {
        $column_name  = oci_field_name($stmt, $i);
        $column_type  = oci_field_type($stmt, $i);
        $column_size  = oci_field_size($stmt, $i);
        echo "Column: $column_name ($column_type, size $column_size)\n";
    }
    oci_free_statement($stmt);
} else {
    $e = oci_error($stmt);
    echo "Error querying remote view: " . $e['message'] . "\n";
}

oci_close($conn);
?>
