<?php
// header("Access-Control-Allow-Origin: * ");
// header('Access-Control-Allow-Credentials: true');
// header('Access-Control-Max-Age: 86400'); // cache for 1 day
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");  
// header("Access-Control-Allow-Headers: Content-type");
// header('Content-type: application/json; charset=utf-8');



require_once 'conn.php';

// echo 'POST';
// print_r($_POST);

// echo 'GET';
// print_r($_GET);

$rawData = file_get_contents('php://input');
$_POST = json_decode($rawData, true);


$username = $_POST['username'];
$rack_num = $_POST['rack_num'];


$sth = $db->prepare("SELECT item_code,item_name1 item_name,qty 
	FROM ZS_VW_AUDIT_REPORT_mob 
	WHERE upper(user_name)='$username' 
		AND upper(rack_num) ='$rack_num' 
	ORDER BY TO_DATE(DATE_SYS, 'DD-MON-YYYY HH24:MI:SS') DESC");
$sth->execute();

//item_code,bin_name,request_date,request_no,qty,

//print("Fetch all of the remaining rows in the result set:\n");
$result = $sth->fetchAll();
print(json_encode($result));


//$insert = $db->prepare("insert into WH.MST_SESSION(SHOP_CODE,ROW_NAME,STATUS) values('$shop_code','$row','Y')") ;

//$insert->execute();



?>

