<?php 

$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
	echo "Connection failed";
    $err = oci_error();
	trigger_error(htmlentities($err['message'], ENT_QUOTES), E_USER_ERROR);	
}



if(isset($_POST['cdiscreport'])){


	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT SHOP_CODE, ITEM_CODE, PRICE, ITEM_NAME, SUM(QTY)QTY, AVG(CURR_STOCK) CURR_STOCK, DEPT
 FROM 
  (
    SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, DEPT, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
    FROM ZS_VW_AUDIT_REPORT a
  ) ab
  WHERE SHOP_CODE = '{$cant}' 
  GROUP BY ITEM_CODE, PRICE, ITEM_NAME, DEPT, SHOP_CODE");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$sqlpending = oci_parse($conn, "SELECT * FROM ZS_VW_AUDIT_PENDING");
    oci_execute($sqlpending);

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Consolidated_Discrepancy_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['SHOP_CODE','ITEM_CODE','ITEM_NAME','AUDIT_QTY','ERP_QTY','DEPT','PRICE','DIFF','DIFF VALUE']);

	while ($row = oci_fetch_assoc($sql)) {
		
		$data = [];
        $data['SHOP_CODE'] = $row['SHOP_CODE'];
        $data['ITEM_CODE'] = $row['ITEM_CODE'];
        $data['ITEM_NAME'] = $row['ITEM_NAME'];
        $data['AUDIT_QTY'] = $row['QTY'];
        $data['ERP_QTY'] = $row['CURR_STOCK'];
        $data['DEPT'] = $row['DEPT'];
        $data['PRICE'] = $row['PRICE'];
        $data['DIFF'] = $data['AUDIT_QTY'] - $data['ERP_QTY'];
        $data['VALUE'] = $data['DIFF'] * $data['PRICE'];

        fputcsv($fp, $data);
	}

	while ($row = oci_fetch_assoc($sqlpending)) {
		
		$data = [];
        $data['SHOP_CODE'] = $row['SHOP_CODE'];
        $data['ITEM_CODE'] = $row['ITEM_CODE'];
        $data['ITEM_NAME'] = $row['ITEM_NAME'];
        $data['AUDIT_QTY'] = 0;
        $data['ERP_QTY'] = $row['CURR_STOCK'];
        $data['DEPT'] = $row['DEPT'];
        $data['PRICE'] = $row['PRICE'];
        $data['DIFF'] = $data['AUDIT_QTY'] - $data['ERP_QTY'];
        $data['VALUE'] = $data['DIFF'] * $data['PRICE'];

        fputcsv($fp, $data);
	}

	echo $name;

}

if(isset($_POST['finalreport'])){

	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT * FROM ZS_STOCK_AUDIT_ERP_NEW WHERE VC_SHOP_CODE = '{$cant}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Final_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['SHOP_CODE','ITEM_CODE','ITEM_NAME','PRICE','AUDIT_QTY','DEPT']);

	while ($row = oci_fetch_assoc($sql)) {
		
		$data = [];
        $data['SHOP_CODE'] = $row['VC_SHOP_CODE'];
        $data['ITEM_CODE'] = $row['VC_ITEM_CODE'];
        $data['ITEM_NAME'] = $row['ITEM_NAME'];
        $data['PRICE'] = $row['PRICE'];
        $data['QTY'] = $row['VC_AUDIT_QTY'];
        $data['DEPT'] = $row['DEPT'];
        

        fputcsv($fp, $data);
	}

	echo $name;

}

if(isset($_POST['provdiscreporte'])){

	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT ITEM_CODE, PRICE, ITEM_NAME, USER_NAME, SUM(QTY)QTY, AVG(CURR_STOCK) CURR_STOCK,rack_num,DATE_SYS
-- (select SUM(QTY)  FROM ZS_VW_AUDIT_REPORT aa WHERE aa.ITEM_CODE = ab.ITEM_CODE GROUP BY ITEM_CODE ) TOTAL_COUNT_QTY
 FROM 
  (
    SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
    FROM ZS_VW_AUDIT_REPORT a
  ) ab
  WHERE SHOP_CODE = '{$cant}' 
  GROUP BY ITEM_CODE, PRICE, ITEM_NAME,USER_NAME,rack_num,DATE_SYS");

	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Provisional_Discrepancy_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','AUDIT_QTY','ERP_QTY','DIFF','DIFF VALUE'/*, 'PRECENTAGE'*/, 'DATE', 'USER', 'RACK']);

	while ($trica = oci_fetch_assoc($sql)) {

		// print_r($trica);

		$tinto = $trica['ITEM_CODE'];
        $item_name = $trica['ITEM_NAME'];
        $item_price = $trica['PRICE'];
        $qty = $trica['QTY'];
        
        $user = $trica['USER_NAME'];
        $rack = $trica['RACK_NUM'];

        // $ttCountQty = $trica['TOTAL_COUNT_QTY'];
        $shop_qty = $trica['CURR_STOCK'] != 0 ? $trica['CURR_STOCK'] : 1;
        $diff = intval($qty) - intval($shop_qty);
        $diff_val = $item_price * $diff;
        // $perc = abs(($diff/intval($shop_qty))*100);
        $Date = $trica['DATE_SYS'];
		
		$data = [$tinto, $item_name, $item_price, $qty, $shop_qty,  $diff, $diff_val/*, $perc*/, $Date, $user, $rack];
        

        fputcsv($fp, $data);
	}

	echo $name;

}

if(isset($_POST['auditfirstrount'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT ITEM_CODE, ITEM_NAME, PRICE, RACK_NUM, USER_NAME, QTY, DATE_SYS, DEPT FROM ZS_VW_AUDIT_REPORT WHERE SHOP_CODE = '{$SHOP_CODE}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Audit_first_round_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM CODE','ITEM NAME','PRICE','RACK','USER', 'QTY', 'DATE/TIME', 'DEPT']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

if(isset($_POST['tobescannede'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND CURR_STOCK <> 0 AND CH_PI = 'N' ORDER BY DEPT");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Items_to_be_scanned_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

if(isset($_POST['zerostocke'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND CURR_STOCK = 0 AND CH_PI = 'N' ORDER BY DEPT");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Items_without_stock_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

if(isset($_POST['allitemse'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM ORDER BY DEPT");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'All_items_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

if(isset($_POST['notallowede'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE CH_PI = 'Y' ORDER BY DEPT ");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Items_not_allowed_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

if(isset($_POST['categorye'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];
	$category = $_POST['cat'];

	$n = str_replace(' ', '_',str_replace('&', 'AND', $category));

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND DEPT = '{$category}' AND CURR_STOCK <>0 AND CH_PI = 'N' ORDER BY ITEM_CODE");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = $n.'_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

if(isset($_POST['uploadedreporte'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT VC_ITEM_CODE,VC_SHOP_CODE,NU_QTY FROM  POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop WHERE vc_shop_code = '{$SHOP_CODE}' ");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Uploaded_items_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM CODE','SHOP CODE','QTY']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

if(isset($_POST['pendingalle'])){

	
	$sql = oci_parse($conn, "SELECT ITEM_CODE, ITEM_NAME, DEPT, (PRICE * CURR_STOCK) As value FROM ZS_VW_AUDIT_PENDING");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'All_pending_items_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','DEPT','VALUE']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

if(isset($_POST['pendingdepte'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];
	$category = $_POST['cat'];

	$n = str_replace(' ', '_',str_replace('&', 'AND', $category));

	$sql = oci_parse($conn, "SELECT ITEM_CODE, ITEM_NAME, DEPT, (PRICE * CURR_STOCK) As value FROM ZS_VW_AUDIT_PENDING WHERE DEPT = '{$category}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = $n.'_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','DEPT','VALUE']);

	while ($row = oci_fetch_assoc($sql)) {
		
        fputcsv($fp, $row);
	}

	echo $name;

}

