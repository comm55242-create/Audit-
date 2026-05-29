<?php
require_once 'conn.php';

$username = $_POST['username'];
$rack_num = $_POST['rack_num'];


$sth = $db->prepare("SELECT item_code,item_name1 item_name,qty FROM ZS_VW_AUDIT_REPORT_mob WHERE upper(user_name)='$username' and upper(rack_num) ='$rack_num' ");
$sth->execute();

//item_code,bin_name,request_date,request_no,qty,

//print("Fetch all of the remaining rows in the result set:\n");
$result = $sth->fetchAll();
print(json_encode($result));


//$insert = $db->prepare("insert into WH.MST_SESSION(SHOP_CODE,ROW_NAME,STATUS) values('$shop_code','$row','Y')") ;

//$insert->execute();



?>

