<?php
$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
    echo "Connection failed\n";
    exit;
}

$sql = oci_parse($conn, "SELECT column_name FROM user_tab_columns WHERE table_name = 'ZS_VW_AUDIT_PENDING'");
oci_execute($sql);
echo "ZS_VW_AUDIT_PENDING columns:\n";
while ($row = oci_fetch_assoc($sql)) {
    echo $row['COLUMN_NAME'] . "\n";
}

$sql2 = oci_parse($conn, "SELECT column_name FROM user_tab_columns WHERE table_name = 'ZS_VW_AUDIT_REPORT'");
oci_execute($sql2);
echo "\nZS_VW_AUDIT_REPORT columns:\n";
while ($row = oci_fetch_assoc($sql2)) {
    echo $row['COLUMN_NAME'] . "\n";
}
?>
