<?php
header("Access-Control-Allow-Origin: * ");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400'); // cache for 1 day
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");  
header("Access-Control-Allow-Headers: Content-type");
header('Content-type: application/json; charset=utf-8');
require_once 'conn.php';

$item_code1 = $_GET['item_code'];

$sth = $db->prepare("SELECT item_code,item_name,barcode,price,dept,shop_code,curr_stock FROM master_item WHERE item_code='$item_code1' OR barcode='$item_code1' ");
$sth->execute();

//item_code,bin_name,request_date,request_no,qty,

//print("Fetch all of the remaining rows in the result set:\n");
$result = $sth->fetchAll();
// print(json_encode($result));
if(count($result)){
	print(json_encode($result[0]));
}else{
	echo 'nodata';
}



//$insert = $db->prepare("insert into WH.MST_SESSION(SHOP_CODE,ROW_NAME,STATUS) values('$shop_code','$row','Y')") ;

//$insert->execute();



?>

