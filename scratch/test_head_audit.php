<?php
$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
    echo "Connection failed";
    exit;
}
$sql = oci_parse($conn, "SELECT * FROM HEAD_AUDIT WHERE ROWNUM <= 10");
oci_execute($sql);
while($row = oci_fetch_assoc($sql)){
    print_r($row);
}
?>
