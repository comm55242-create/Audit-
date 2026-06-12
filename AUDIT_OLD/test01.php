<?php
$conn = oci_connect('SHOP', 'SHOP', 'localhost/ORCL');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
	$NAME=$_POST ['NAME'];
	$ADDRESS= $_POST['ADDRESS'];
	
	$query ="insert into shop.test (NAME,ADDRESS) values('$NAME','$ADDRESS')";
	
	 $result = oci_parse($conn, $query);
    oci_execute($result);
	
		
	?>
	