<?php 
$seen = [];

$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
	echo "Connection failed";
    $err = oci_error();
	trigger_error(htmlentities($err['message'], ENT_QUOTES), E_USER_ERROR);	
}

// Ensure the export directory exists before trying to create files inside it
if (!file_exists('../export')) {
    mkdir('../export', 0777, true);
}



if(isset($_POST['cdiscreport'])){


	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT ab.SHOP_CODE, ab.ITEM_CODE, ab.PRICE, ab.ITEM_NAME, SUM(ab.QTY)QTY, AVG(ab.CURR_STOCK) CURR_STOCK, ab.DEPT, MAX(mi.VC_GROUP) AS VC_GROUP, MAX(mi.VC_SUBGROUP) AS VC_SUBGROUP
 FROM 
  (
    SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, DEPT, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
    FROM ZS_VW_AUDIT_REPORT a
  ) ab
  LEFT JOIN MASTER_ITEM mi ON ab.ITEM_CODE = mi.ITEM_CODE
  WHERE ab.SHOP_CODE = '{$cant}' 
  GROUP BY ab.ITEM_CODE, ab.PRICE, ab.ITEM_NAME, ab.DEPT, ab.SHOP_CODE");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$sqlpending = oci_parse($conn, "SELECT p.*, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_VW_AUDIT_PENDING p LEFT JOIN MASTER_ITEM mi ON p.ITEM_CODE = mi.ITEM_CODE");
    oci_execute($sqlpending);

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Consolidated_Discrepancy_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['SHOP_CODE', 'ITEM_CODE', 'ITEM_NAME', 'AUDIT_QTY', 'ERP_QTY', 'PRICE', 'DIFF', 'DIFF VALUE', 'DEPT', 'GROUP', 'SUB-GROUP', 'ZONES']);
	
	$seenRows = [];

    // 1. Fetch Distinct Zones per Item
    $zone_query = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, RACK_NUM FROM ZS_VW_AUDIT_REPORT WHERE SHOP_CODE = '{$cant}'");
    oci_execute($zone_query);
    $item_zones = [];
    while ($zrow = oci_fetch_array($zone_query)) {
        $item_zones[$zrow['ITEM_CODE']][] = $zrow['RACK_NUM'];
    }

	while ($row = oci_fetch_assoc($sql)) {
        $audit_qty = isset($row['QTY']) ? $row['QTY'] : 0;
        $erp_qty = isset($row['CURR_STOCK']) ? $row['CURR_STOCK'] : 0;
        $price = isset($row['PRICE']) ? $row['PRICE'] : 0;
        $diff = $audit_qty - $erp_qty;
        $diff_val = $diff * $price;
        $zones = isset($item_zones[$row['ITEM_CODE']]) ? implode(',', $item_zones[$row['ITEM_CODE']]) : '';
        
		$dataArray = [
            $row['SHOP_CODE'],
            $row['ITEM_CODE'],
            $row['ITEM_NAME'],
            $audit_qty,
            $erp_qty,
            $price,
            $diff,
            $diff_val,
            $row['DEPT'],
            isset($row['VC_GROUP']) ? $row['VC_GROUP'] : '',
            isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : '',
            $zones // ZONES
        ];
        
        $hash = md5(serialize($dataArray));
        if(!isset($seenRows[$hash])) {
            $seenRows[$hash] = true;
            fputcsv($fp, $dataArray);
        }
	}

	while ($row = oci_fetch_assoc($sqlpending)) {
        $audit_qty = 0;
        $erp_qty = isset($row['CURR_STOCK']) ? $row['CURR_STOCK'] : 0;
        $price = isset($row['PRICE']) ? $row['PRICE'] : 0;
        $diff = $audit_qty - $erp_qty;
        $diff_val = $diff * $price;
        
		$dataArray = [
            $row['SHOP_CODE'],
            $row['ITEM_CODE'],
            $row['ITEM_NAME'],
            $audit_qty,
            $erp_qty,
            $price,
            $diff,
            $diff_val,
            $row['DEPT'],
            isset($row['VC_GROUP']) ? $row['VC_GROUP'] : '',
            isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : '',
            '' // ZONES
        ];
        
        $hash = md5(serialize($dataArray));
        if(!isset($seenRows[$hash])) {
            $seenRows[$hash] = true;
            fputcsv($fp, $dataArray);
        }
	}

	echo $name;

}

if(isset($_POST['finalreport'])){

	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT a.*, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_STOCK_AUDIT_ERP_NEW a LEFT JOIN MASTER_ITEM mi ON a.VC_ITEM_CODE = mi.ITEM_CODE WHERE a.VC_SHOP_CODE = '{$cant}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Final_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['SHOP_CODE','ITEM_CODE','ITEM_NAME','PRICE','AUDIT_QTY','DEPT','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
		$data = [];
        $data['SHOP_CODE'] = $row['VC_SHOP_CODE'];
        $data['ITEM_CODE'] = $row['VC_ITEM_CODE'];
        $data['ITEM_NAME'] = $row['ITEM_NAME'];
        $data['PRICE'] = $row['PRICE'];
        $data['QTY'] = $row['VC_AUDIT_QTY'];
        $data['DEPT'] = $row['DEPT'];
        $data['GROUP'] = isset($row['VC_GROUP']) ? $row['VC_GROUP'] : '';
        $data['SUB_GROUP'] = isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : '';

        if(!isset($seen[md5(serialize($data))])){ $seen[md5(serialize($data))] = true; fputcsv($fp, $data); }
	}

	echo $name;

}

if(isset($_POST['provdiscreporte'])){

	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT ab.ITEM_CODE, ab.PRICE, ab.ITEM_NAME, ab.USER_NAME, SUM(ab.QTY)QTY, AVG(ab.CURR_STOCK) CURR_STOCK, ab.rack_num, ab.DATE_SYS, MAX(mi.VC_GROUP) AS VC_GROUP, MAX(mi.VC_SUBGROUP) AS VC_SUBGROUP
-- (select SUM(QTY)  FROM ZS_VW_AUDIT_REPORT aa WHERE aa.ITEM_CODE = ab.ITEM_CODE GROUP BY ITEM_CODE ) TOTAL_COUNT_QTY
 FROM 
  (
    SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
    FROM ZS_VW_AUDIT_REPORT a
  ) ab
  LEFT JOIN MASTER_ITEM mi ON ab.ITEM_CODE = mi.ITEM_CODE
  WHERE ab.SHOP_CODE = '{$cant}' 
  GROUP BY ab.ITEM_CODE, ab.PRICE, ab.ITEM_NAME, ab.USER_NAME, ab.rack_num, ab.DATE_SYS");

	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Provisional_Discrepancy_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','AUDIT_QTY','ERP_QTY','DIFF','DIFF VALUE'/*, 'PRECENTAGE'*/, 'DATE', 'USER', 'RACK', 'GROUP', 'SUB-GROUP']);

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
        $grp = isset($trica['VC_GROUP']) ? $trica['VC_GROUP'] : '';
        $sgrp = isset($trica['VC_SUBGROUP']) ? $trica['VC_SUBGROUP'] : '';
		
		$data = [$tinto, $item_name, $item_price, $qty, $shop_qty,  $diff, $diff_val/*, $perc*/, $Date, $user, $rack, $grp, $sgrp];
        

        if(!isset($seen[md5(serialize($data))])){ $seen[md5(serialize($data))] = true; fputcsv($fp, $data); }
	}

	echo $name;

}

if(isset($_POST['auditfirstrount'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT a.ITEM_CODE, a.ITEM_NAME, a.PRICE, a.RACK_NUM, a.USER_NAME, a.QTY, a.DATE_SYS, a.DEPT, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_VW_AUDIT_REPORT a LEFT JOIN MASTER_ITEM mi ON a.ITEM_CODE = mi.ITEM_CODE WHERE a.SHOP_CODE = '{$SHOP_CODE}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Audit_first_round_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM CODE','ITEM NAME','PRICE','RACK','USER', 'QTY', 'DATE/TIME', 'DEPT','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['tobescannede'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT, VC_GROUP, VC_SUBGROUP FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND CURR_STOCK <> 0 AND CH_PI = 'N' ORDER BY DEPT");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Items_to_be_scanned_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['zerostocke'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT, VC_GROUP, VC_SUBGROUP FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND CURR_STOCK = 0 AND CH_PI = 'N' ORDER BY DEPT");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Items_without_stock_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['allitemse'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT, VC_GROUP, VC_SUBGROUP FROM MASTER_ITEM ORDER BY DEPT");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'All_items_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['notallowede'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT, VC_GROUP, VC_SUBGROUP FROM MASTER_ITEM WHERE CH_PI = 'Y' ORDER BY DEPT ");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Items_not_allowed_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['categorye'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];
	$category = $_POST['cat'];

	$n = str_replace(' ', '_',str_replace('&', 'AND', $category));

	$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT, VC_GROUP, VC_SUBGROUP FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND DEPT = '{$category}' AND CURR_STOCK <>0 AND CH_PI = 'N' ORDER BY ITEM_CODE");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = $n.'_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['uploadedreporte'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT t.VC_ITEM_CODE, t.VC_SHOP_CODE, t.NU_QTY, mi.VC_GROUP, mi.VC_SUBGROUP FROM POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop t LEFT JOIN MASTER_ITEM mi ON t.VC_ITEM_CODE = mi.ITEM_CODE WHERE t.vc_shop_code = '{$SHOP_CODE}' ");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Uploaded_items_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM CODE','SHOP CODE','QTY','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['pendingalle'])){

	
	$sql = oci_parse($conn, "SELECT p.*, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_VW_AUDIT_PENDING p LEFT JOIN MASTER_ITEM mi ON p.ITEM_CODE = mi.ITEM_CODE");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'All_pending_items_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['SHOP_CODE', 'ITEM_CODE', 'ITEM_NAME', 'AUDIT_QTY', 'ERP_QTY', 'PRICE', 'DIFF', 'DIFF VALUE', 'DEPT', 'GROUP', 'SUB-GROUP', 'ZONES']);

    $seenRows = [];

	while ($row = oci_fetch_assoc($sql)) {
        $audit_qty = 0;
        $erp_qty = isset($row['CURR_STOCK']) ? $row['CURR_STOCK'] : 0;
        $price = isset($row['PRICE']) ? $row['PRICE'] : 0;
        $diff = $audit_qty - $erp_qty;
        $diff_val = $diff * $price;
        
		$dataArray = [
            isset($row['SHOP_CODE']) ? $row['SHOP_CODE'] : '',
            $row['ITEM_CODE'],
            $row['ITEM_NAME'],
            $audit_qty,
            $erp_qty,
            $price,
            $diff,
            $diff_val,
            $row['DEPT'],
            isset($row['VC_GROUP']) ? $row['VC_GROUP'] : '',
            isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : '',
            '' // ZONES
        ];
        
        $hash = md5(serialize($dataArray));
        if(!isset($seenRows[$hash])) {
            $seenRows[$hash] = true;
            fputcsv($fp, $dataArray);
        }
	}

	echo $name;

}

if(isset($_POST['pendingdepte'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];
	$category = $_POST['cat'];

	$n = str_replace(' ', '_',str_replace('&', 'AND', $category));

	$sql = oci_parse($conn, "SELECT p.ITEM_CODE, p.ITEM_NAME, p.DEPT, (p.PRICE * p.CURR_STOCK) As value, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_VW_AUDIT_PENDING p LEFT JOIN MASTER_ITEM mi ON p.ITEM_CODE = mi.ITEM_CODE WHERE p.DEPT = '{$category}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = $n.'_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','DEPT','VALUE','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['audit2report'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT a.ITEM_CODE, a.ITEM_NAME, a.PRICE, a.RACK_NUM, a.USER_NAME, a.QTY, a.DATE_SYS, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_VW_AUDIT_REPORT_round a LEFT JOIN MASTER_ITEM mi ON a.ITEM_CODE = mi.ITEM_CODE WHERE a.SHOP_CODE = '{$SHOP_CODE}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Audit_2_Round_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM CODE','ITEM NAME','PRICE','RACK','USER', 'QTY', 'DATE/TIME','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['stockauditerp'])){

	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT a.shop_code, a.ITEM_CODE, a.AUDIT_QTY, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_STOCK_AUDIT_ERP a LEFT JOIN MASTER_ITEM mi ON a.ITEM_CODE = mi.ITEM_CODE WHERE a.VC_SHOP_CODE = '{$cant}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Stock_Audit_ERP_report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['SHOP_CODE','ITEM_CODE','AUDIT_QTY','GROUP','SUB-GROUP']);

	while ($row = oci_fetch_assoc($sql)) {
		
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;

}

if(isset($_POST['viewreportse'])){

	session_start();
	$SHOP_CODE = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT ITEM_CODE, QTY, DATE_SYS FROM HEAD_AUDIT WHERE SHOP_CODE = '{$SHOP_CODE}'");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Raw_View_Reports_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','QTY','DATE/TIME']);

	while ($row = oci_fetch_assoc($sql)) {
        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }
	}

	echo $name;
}
if(isset($_POST['recountingreport'])){

	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT ab.SHOP_CODE, ab.ITEM_CODE, ab.PRICE, ab.ITEM_NAME, SUM(ab.QTY)QTY, AVG(ab.CURR_STOCK) CURR_STOCK, ab.DEPT, MAX(mi.VC_GROUP) AS VC_GROUP, MAX(mi.VC_SUBGROUP) AS VC_SUBGROUP
 FROM 
  (
    SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, DEPT, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
    FROM ZS_VW_AUDIT_REPORT a
  ) ab
  LEFT JOIN MASTER_ITEM mi ON ab.ITEM_CODE = mi.ITEM_CODE AND ab.SHOP_CODE = mi.SHOP_CODE
  WHERE ab.SHOP_CODE = '{$cant}' 
  GROUP BY ab.ITEM_CODE, ab.PRICE, ab.ITEM_NAME, ab.DEPT, ab.SHOP_CODE");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$sqlpending = oci_parse($conn, "SELECT p.*, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_VW_AUDIT_PENDING p LEFT JOIN MASTER_ITEM mi ON p.ITEM_CODE = mi.ITEM_CODE AND p.SHOP_CODE = mi.SHOP_CODE WHERE p.SHOP_CODE = '{$cant}'");
    oci_execute($sqlpending);

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Recounting_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PHYSICAL_QTY','PRICE','DIFF QTY','DIFF VALUE','DEPT','GROUPS','SUBGROUPS']);

	while ($row = oci_fetch_assoc($sql)) {
		
		$data = [];
        $data['ITEM_CODE'] = $row['ITEM_CODE'];
        $data['ITEM_NAME'] = $row['ITEM_NAME'];
        $data['PHYSICAL_QTY'] = '';
        $data['PRICE'] = $row['PRICE'];
        $data['DIFF QTY'] = $row['QTY'] - $row['CURR_STOCK'];
        $data['DIFF VALUE'] = $data['DIFF QTY'] * $data['PRICE'];
        $data['DEPT'] = $row['DEPT'];
        $data['GROUPS'] = isset($row['VC_GROUP']) ? $row['VC_GROUP'] : '';
        $data['SUBGROUPS'] = isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : '';
        if(!isset($seen[md5(serialize($data))])){ $seen[md5(serialize($data))] = true; fputcsv($fp, $data); }
	}

	while ($row = oci_fetch_assoc($sqlpending)) {
		
		$data = [];
        $data['ITEM_CODE'] = $row['ITEM_CODE'];
        $data['ITEM_NAME'] = $row['ITEM_NAME'];
        $data['PHYSICAL_QTY'] = '';
        $data['PRICE'] = $row['PRICE'];
        $data['DIFF QTY'] = 0 - $row['CURR_STOCK'];
        $data['DIFF VALUE'] = $data['DIFF QTY'] * $data['PRICE'];
        $data['DEPT'] = $row['DEPT'];
        $data['GROUPS'] = isset($row['VC_GROUP']) ? $row['VC_GROUP'] : '';
        $data['SUBGROUPS'] = isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : '';
        if(!isset($seen[md5(serialize($data))])){ $seen[md5(serialize($data))] = true; fputcsv($fp, $data); }
	}

	echo $name;

}

if(isset($_POST['variancereport'])){

	session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT a.VC_ITEM_CODE AS ITEM_CODE, MAX(a.ITEM_NAME) AS ITEM_NAME, MAX(a.PRICE) AS PRICE, MAX(a.VC_AUDIT_QTY) AS QTY, MAX(a.DEPT) AS DEPT, MAX(mi.CURR_STOCK) AS CURR_STOCK, MAX(mi.VC_GROUP) AS VC_GROUP, MAX(mi.VC_SUBGROUP) AS VC_SUBGROUP
 FROM ZS_STOCK_AUDIT_ERP_NEW a
 LEFT JOIN MASTER_ITEM mi ON a.VC_ITEM_CODE = mi.ITEM_CODE AND a.VC_SHOP_CODE = mi.SHOP_CODE
 WHERE a.VC_SHOP_CODE = '{$cant}'
 GROUP BY a.VC_ITEM_CODE");
	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$sqlpending = oci_parse($conn, "SELECT p.ITEM_CODE, MAX(p.ITEM_NAME) AS ITEM_NAME, MAX(p.PRICE) AS PRICE, MAX(p.DEPT) AS DEPT, MAX(p.CURR_STOCK) AS CURR_STOCK, MAX(mi.VC_GROUP) AS VC_GROUP, MAX(mi.VC_SUBGROUP) AS VC_SUBGROUP FROM ZS_VW_AUDIT_PENDING p LEFT JOIN MASTER_ITEM mi ON p.ITEM_CODE = mi.ITEM_CODE AND p.SHOP_CODE = mi.SHOP_CODE WHERE p.SHOP_CODE = '{$cant}' GROUP BY p.ITEM_CODE");
    oci_execute($sqlpending);

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Variance_Report_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['ITEM_CODE','ITEM_NAME','PRICE','DEPT','GROUPS','SUBGROUPS','ERP_QTY','AUDIT_QTY','DIFF','DIFF VALUE','ZONES']);

    // 1. Fetch Distinct Zones per Item
    $zone_query = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, RACK_NUM FROM ZS_VW_AUDIT_REPORT WHERE SHOP_CODE = '{$cant}'");
    oci_execute($zone_query);
    $item_zones = [];
    while ($zrow = oci_fetch_assoc($zone_query)) {
        $icode = $zrow['ITEM_CODE'];
        if (!isset($item_zones[$icode])) {
            $item_zones[$icode] = [];
        }
        $item_zones[$icode][] = $zrow['RACK_NUM'];
    }

	while ($row = oci_fetch_assoc($sql)) {
		
		$data = [];
        $data['ITEM_CODE'] = $row['ITEM_CODE'];
        $data['ITEM_NAME'] = $row['ITEM_NAME'];
        $data['PRICE'] = $row['PRICE'];
        $data['DEPT'] = $row['DEPT'];
        $data['GROUPS'] = isset($row['VC_GROUP']) ? $row['VC_GROUP'] : '';
        $data['SUBGROUPS'] = isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : '';
        $data['ERP_QTY'] = $row['CURR_STOCK'];
        $data['AUDIT_QTY'] = $row['QTY'];
        $data['DIFF'] = $row['QTY'] - $row['CURR_STOCK'];
        $data['DIFF VALUE'] = $data['DIFF'] * $data['PRICE'];
        
        $zones_string = isset($item_zones[$row['ITEM_CODE']]) ? implode(", ", $item_zones[$row['ITEM_CODE']]) : '';
        $data['ZONES'] = $zones_string;

        if(!isset($seen[md5(serialize($data))])){ $seen[md5(serialize($data))] = true; fputcsv($fp, $data); }
	}

	while ($row = oci_fetch_assoc($sqlpending)) {
		
		$data = [];
        $data['ITEM_CODE'] = $row['ITEM_CODE'];
        $data['ITEM_NAME'] = $row['ITEM_NAME'];
        $data['PRICE'] = $row['PRICE'];
        $data['DEPT'] = $row['DEPT'];
        $data['GROUPS'] = isset($row['VC_GROUP']) ? $row['VC_GROUP'] : '';
        $data['SUBGROUPS'] = isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : '';
        $data['ERP_QTY'] = $row['CURR_STOCK'];
        $data['AUDIT_QTY'] = 0;
        $data['DIFF'] = $data['AUDIT_QTY'] - $data['ERP_QTY'];
        $data['DIFF VALUE'] = $data['DIFF'] * $data['PRICE'];
        $data['ZONES'] = '';
        
        if(!isset($seen[md5(serialize($data))])){ $seen[md5(serialize($data))] = true; fputcsv($fp, $data); }
	}

	echo $name;

}
?>
