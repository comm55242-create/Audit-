<?php

header("Access-Control-Allow-Origin: * ");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");  

require_once 'conn.php';

	$rawData = file_get_contents('php://input');
	$_POST = json_decode($rawData, true);

	$shop_code= $_POST['shop_code'];
	$item_code= $_POST['item_code'];
	$qty = $_POST['qty'];
	
	$user = $_POST['user'];
	$ip= $_POST['ip'];
	$rack= $_POST['rack'];

	// --- MANAGER REQUIREMENT: Archive and Truncate HEAD_AUDIT before new insert ---
	// try {
	//     $db->exec("INSERT INTO head_audit_archive SELECT * FROM head_audit");
	//     $db->exec("TRUNCATE TABLE head_audit");
	// } catch (Exception $e) { }
	// -----------------------------------------------------------------------------

	$query = $db->prepare("insert into head_audit (shop_code,item_code,qty,emp_code,IP,user_name,rack_num) values ('$shop_code','$item_code','$qty','$user','$ip','$user','$rack' )");
	
	$query->execute();


	
	echo "ok";
  
// 	?>
