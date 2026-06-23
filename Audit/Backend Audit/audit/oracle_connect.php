<?php

$tns = "  
(DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    )
    (CONNECT_DATA =
      (SERVICE_NAME = orcl)
    )
  )
       ";
$db_username = "SHOP";
$db_password = "SHOP";

try {
	// mysql connection
	$db = new PDO("oci:dbname=".$tns,$db_username,$db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // echo "Connection successful!";
} catch (PDOException $e) {
	echo "Error: " . $e->getMessage();
}

?>

