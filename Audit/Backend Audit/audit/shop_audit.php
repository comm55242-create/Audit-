<?php
require_once 'conn.php';

$item_code1 = $_POST['item_code'];

$sth = $db->prepare("SELECT item_code,item_name,barcode,price,dept,shop_code,curr_stock, vc_unit as UNIT FROM master_item WHERE item_code='$item_code1' OR barcode='$item_code1' ");
$sth->execute();

//item_code,bin_name,request_date,request_no,qty,

//print("Fetch all of the remaining rows in the result set:\n");
$result = $sth->fetchAll();
print(json_encode($result));


//$insert = $db->prepare("insert into WH.MST_SESSION(SHOP_CODE,ROW_NAME,STATUS) values('$shop_code','$row','Y')") ;

//$insert->execute();



?>

