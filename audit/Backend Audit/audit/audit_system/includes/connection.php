<?php
// 1. Create a datbase connection DB_PASS
$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = localhost (SID = orcl)))');
if(!$conn){
	echo "Connection failed";
    $err = oci_error();
	trigger_error(htmlentities($err['message'], ENT_QUOTES), E_USER_ERROR);	

}

?>