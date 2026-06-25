<?php
$conn=oci_connect('SHOP','SHOP','(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=localhost)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=orcl)))');
$s=oci_parse($conn,"SELECT * FROM USERS WHERE USERNAME='ADMIN'");
oci_execute($s);
print_r(oci_fetch_assoc($s));
?>
