<?php

require_once("includes/session.php");
require_once("includes/connection.php");
$SHOP_CODE = $_SESSION['storecode'];

$ajax_data = file_get_contents("php://input");
$request = json_decode($ajax_data);

if (property_exists($request, "rack")) {
	$txtbarcode = $request->barcode;
	$rack = $request->rack;

	$result = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, BARCODE, PRICE FROM MASTER_ITEM WHERE BARCODE = UPPER('{$txtbarcode}') ");
	if (!$result) {
		$e = oci_error($conn);
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	} 
	$r = oci_execute($result);

	$toto = oci_fetch_array($result);
	$item_code = $toto['ITEM_CODE'];
	$item_name = $toto['ITEM_NAME'];

	$ajax_result['item_code'] = $item_code;
	$ajax_result['item_name'] = $item_name;
	$ajax_result['barcode'] = $toto['BARCODE'];
	$ajax_result['price'] = $toto['PRICE'];
	$ajax_result['rack_num'] = $rack;

	$sql = oci_parse($conn, "SELECT ITEM_CODE, SUM(QTY) QTY, DATE_SYS, EMP_CODE FROM HEAD_AUDIT WHERE ITEM_CODE = '{$item_code}' AND SHOP_CODE = '{$SHOP_CODE}' AND QTY <> 0 GROUP BY ITEM_CODE, DATE_SYS, EMP_CODE");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$r2 = oci_execute($sql);

	$trica = oci_fetch_array($sql);
	$tinto = $trica['ITEM_CODE'];
	$Qty = $trica['QTY'];
	$Date = $trica['DATE_SYS'];
	$emp = $trica['EMP_CODE'];

	$sql2 = oci_parse($conn, "SELECT E_NAME FROM USERS WHERE STAFF_ID = '{$emp}'");
	if (!$sql2) {
		$e3 = oci_error($conn);
		trigger_error(htmlentities($e3['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$r3 = oci_execute($sql2);

	$ben = oci_fetch_array($sql2);
	$e_name2 = $ben['E_NAME'];

	if ($item_name == '') {
		http_response_code(404);
		echo "No Record Found";
	} else {
		if ($item_code == $tinto) {
			$ajax_result['confirmation'] = "Item Code {$tinto} has been counted by {$e_name2} with latest count of $Qty Items on $Date. Do you want to update this record?";
		}
		$ajax_result = json_encode($ajax_result);
		echo $ajax_result;
	}


} elseif (property_exists($request, "qty")) {

	$quantity = $request->qty;
	$barcode = $request->barcode;
	$item_code = $request->item_code;
	$rack_num = $request->rack_num;

	$user_name = $_SESSION['username'];
	$s_code = $_SESSION['storecode'];
	$staff_id = $_SESSION['staff_id'];
	$dtime = date('Y-m-d H:i:s');
	$IP = $_SERVER["REMOTE_ADDR"];

	$query = "INSERT INTO HEAD_AUDIT (
					SHOP_CODE, ITEM_CODE, QTY, DATE_SYS, EMP_CODE, IP, USER_NAME, RACK_NUM
				)VALUES(
				'{$s_code}', '{$item_code}', '{$quantity}', '{$dtime}', '{$staff_id}', '{$IP}', '{$user_name}', '{$rack_num}'
				)";
	$update = oci_parse($conn, $query);
	oci_execute($update);
	if($update){
		echo "Update Successful!";
	} else {
		http_response_code(404);
		echo "Product Audit Failed";
	}

}

?>