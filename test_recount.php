<?php
$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
    echo "Connection failed\n";
    exit;
}

$sql = oci_parse($conn, "SELECT column_name FROM user_tab_columns WHERE table_name = 'RECOUNT_APPROVALS'");
oci_execute($sql);
echo "RECOUNT_APPROVALS columns:\n";
$found = false;
while ($row = oci_fetch_assoc($sql)) {
    echo $row['COLUMN_NAME'] . "\n";
    $found = true;
}
if (!$found) echo "Table not found!\n";
?>
