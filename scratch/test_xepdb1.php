<?php
$conn = oci_connect('SHOP', 'SHOP', '(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = xepdb1)
    )
  )');

if (!$conn) {
    $e = oci_error();
    echo "Connection failed: " . $e['message'] . "\n";
    exit;
}
echo "Connection successful to XEPDB1!\n";
?>
