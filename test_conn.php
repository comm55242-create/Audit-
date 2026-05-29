<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Connecting to Oracle Database at 10.10.0.202:1521/orcl...\n";

$conn = oci_connect('SHOP', 'SHOP', '(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = orcl)
    )
  )');

if (!$conn) {
    $e = oci_error();
    echo "Connection failed!\n";
    echo "Error Message: " . $e['message'] . "\n";
    exit;
}

echo "Connection Successful! Listing tables:\n";

$query = 'SELECT table_name FROM user_tables ORDER BY table_name';
$statement = oci_parse($conn, $query);
oci_execute($statement);

$count = 0;
while ($row = oci_fetch_array($statement, OCI_ASSOC)) {
    $count++;
    echo $count . ". " . $row['TABLE_NAME'] . "\n";
}

if ($count === 0) {
    echo "No tables found in this schema.\n";
}

oci_free_statement($statement);
oci_close($conn);
?>
