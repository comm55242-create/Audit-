<?php
/*$conn = oci_connect('WH', 'WH', 'localhost:1521/XE');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
*/
require_once 'conn.php';

	$shop_code= $_POST['shop_code'];
	$item_code= $_POST['item_code'];
	$qty = $_POST['qty'];
	//$date_sys = $_POST['date_sys'];
	$user = $_POST['user'];
	$ip= $_POST['ip'];
	$rack= $_POST['rack'];
	
	
	//DATETIME = $_POST['DATETIME'];

	
	$query =$db->prepare("insert into head_audit (shop_code,item_code,qty,emp_code,IP,user_name,rack_num) values ('$shop_code','$item_code','$qty','$user','$ip','$user','$rack' )");
	
	$query->execute();
	
	
//	return("Commit Successful");
  
	echo "Sucess";
  
	?>
	