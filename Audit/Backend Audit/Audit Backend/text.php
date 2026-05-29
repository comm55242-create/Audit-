<?php 


$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
	echo "Connection failed";
    $err = oci_error();
	trigger_error(htmlentities($err['message'], ENT_QUOTES), E_USER_ERROR);	
}





$deptQuery = "";
$deptCodeArray = ['ELECTRICAL APPLIANCES', 'FURNITURE'];

$str = '';
for($i = 0; $i < count($deptCodeArray); $i++){
	
	if($i == 0){
		$str = $str."'".$deptCodeArray[$i]."'";
	}else{
		$str = $str.",'".$deptCodeArray[$i]."'";
	}
}

$deptQuery = "WHERE DEPT IN (".$str.")";

// echo $str;

$sql = oci_parse($conn, "SELECT * FROM 
(select item_code,item_name,barcode,price,dept,S.NU_BALANCE_QTY,S.VC_SHOP_CODE,A.CH_PI, A.CH_STATUS
	from makess.vs_item_audit_final@db_link_shop a
 	left outer join pos.shop_stock_summary@db_link_shop s on s.vc_item_code=a.item_code and s.vc_comp_code='01'
	and s.vc_shop_code='MKL'
) 
".$deptQuery."");
oci_execute($sql);

while($trica = oci_fetch_array($sql)){

	print_r($trica);
	echo '<br>';
}




exit();
setcookie('tic', 1, time() + (86400 * 30), "/");
for($i = 0; $i < 10000; $i++){
	

	echo $i;

	// if($_COOKIE['tic']){
	// 	echo $_COOKIE['tic'];
	// }

	// setcookie('tic', $i, time() + (86400 * 30), "/"); // 86400 = 1 day
	
	echo '<br>'; 
}

exit();
session_start();
	$cant = $_SESSION['storecode'];

	$sql = oci_parse($conn, "SELECT ITEM_CODE, PRICE, ITEM_NAME, USER_NAME, SUM(QTY)QTY, AVG(CURR_STOCK) CURR_STOCK,rack_num,DATE_SYS 
	FROM 
	(
	SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
	FROM ZS_VW_AUDIT_REPORT a
	) 
	WHERE SHOP_CODE = 'MKL' 
	GROUP BY ITEM_CODE, PRICE, ITEM_NAME,USER_NAME,rack_num,DATE_SYS");

	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$r2 = oci_execute($sql);
	// $dtime = date('Y-m-d_H-i-s');
	// $name = 'Provisional_Discrepancy_Report_'.$dtime.'.csv';
	// $dron2 = '../export/'.$name;
	// $fp = fopen($dron2, 'w');

	// fputcsv($fp, ['SHOP_CODE','ITEM_CODE','ITEM_NAME','AUDIT_QTY','ERP_QTY','DEPT','PRICE','DIFF','DIFF VALUE']);

	while ($trica = oci_fetch_assoc($sql)) {

		print_r($trica);

		// $tinto = $trica['ITEM_CODE'];
  //       $item_name = $trica['ITEM_NAME'];
  //       $item_price = $trica['PRICE'];
  //       $qty = $trica['QTY'];
        
  //       $user = $trica['USER_NAME'];
  //       $rack = $trica['RACK_NUM'];

  //       $shop_qty = $trica['CURR_STOCK'] != 0 ? $trica['CURR_STOCK'] : 1;
  //       $diff = intval($qty) - intval($shop_qty);
  //       $diff_val = $item_price * $diff;
  //       $perc = abs(($diff/intval($shop_qty))*100);
  //       $Date = $trica['DATE_SYS'];
		
		// $data = [$tinto, $item_name, $item_price, $qty, $shop_qty, $diff, $diff_val, $perc, $Date, $user, $rack];
        

  //       fputcsv($fp, $data);
	}

	
exit();
$sql = oci_parse($conn, "SELECT * FROM ZS_STOCK_AUDIT_ERP_NEW WHERE VC_SHOP_CODE = 'MKL'");
  if (!$sql) {
    $e2 = oci_error($conn);
    trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
  }
  $r2 = oci_execute($sql);
  // $dtime = date('Y-m-d_H-i-s');
  // $dron2 = 'C:\report\Initial_stock_count_'.$dtime.'.csv';
  // $fp = fopen($dron2, 'w');

  while ($row = oci_fetch_assoc($sql)) {
    fputcsv($fp, $row);
    print_r($row);
    // error_reporting(0);
  }

exit();
// $sc = $_POST['sc'];

	$sqlcount = oci_parse($conn, "select COUNT(VC_ITEM_CODE) sc FROM  POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop WHERE vc_shop_code='ACH' ");

	if (!$sqlcount) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$r2count = oci_execute($sqlcount);

	$totalCount = 0;
	while ($tricacount = oci_fetch_array($sqlcount)) {
		$totalCount = $tricacount[0];
	}

	echo $totalCount;
exit();
$sql = oci_parse($conn, "SELECT ITEM_CODE,SHOP_CODE,SUM(QTY) qty FROM HEAD_AUDIT
	WHERE audit_round='1'
	GROUP BY ITEM_CODE,SHOP_CODE
	UNION ALL
	 select distinct ITEM_CODE,SHOP_CODE,0 from  MASTER_ITEM B1
	WHERE
	item_code not in (
	select distinct item_code from HEAD_AUDIT
	 )
	AND CURR_STOCK<>0");

	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$r2 = oci_execute($sql);

	
	while ($trica = oci_fetch_array($sql)) {

		// echo $trica['ITEM_CODE'].'<br>';
		print_r($trica);

		// $tic = $bdd->query('UPDATE count SET count = count + 1 WHERE id = 1');

	}

	
	oci_free_statement($sql);


exit();
$sqlcount = oci_parse($conn, "SELECT COUNT(ITEM_CODE) co FROM MASTER_ITEM_TEST");

	if (!$sqlcount) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$r2count = oci_execute($sqlcount);

	$totalCount = 0;
	while ($tricacount = oci_fetch_array($sqlcount)) {
		$totalCount = $tricacount[0];
	}

	echo $totalCount;
	
exit();
$sqlinsert = oci_parse($conn, "INSERT INTO MASTER_ITEM_TEST (item_code,item_name,barcode,price,dept,CURR_STOCK,SHOP_CODE)
	select item_code,item_name,barcode,price,dept,S.NU_BALANCE_QTY,S.VC_SHOP_CODE
	from makess.vs_item_audit_final@db_link_shop a
 	left outer join pos.shop_stock_summary@db_link_shop s on s.vc_item_code=a.item_code and s.vc_comp_code='01'
	and s.vc_shop_code='ACH'");
$rqlinsert = oci_execute($sqlinsert);

exit();
$sql = oci_parse($conn, "SELECT * FROM MASTER_ITEM  ");

	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$r2 = oci_execute($sql);

	
	while ($trica = oci_fetch_array($sql)) {

		// $tic = $bdd->query('UPDATE count SET count = count + 1 WHERE id = 1');

		$sqlinsert = oci_parse($conn, "INSERT INTO MASTER_ITEM_TEST VALUES(:ITEM_CODE, :ITEM_NAME, :BARCODE, :IMAGE, :PRICE, :DEPT, :SHOP_CODE, :CURR_STOCK) ");
			
			oci_bind_by_name($sqlinsert, ':ITEM_CODE', $trica['ITEM_CODE']);
			oci_bind_by_name($sqlinsert, ':ITEM_NAME', $trica['ITEM_NAME']);
			oci_bind_by_name($sqlinsert, ':BARCODE', $trica['BARCODE']);
			oci_bind_by_name($sqlinsert, ':IMAGE', $trica['IMAGE']);
			oci_bind_by_name($sqlinsert, ':PRICE', $trica['PRICE']);
			oci_bind_by_name($sqlinsert, ':DEPT', $trica['DEPT']);
			oci_bind_by_name($sqlinsert, ':SHOP_CODE', $trica['SHOP_CODE']);
			oci_bind_by_name($sqlinsert, ':CURR_STOCK', $trica['CURR_STOCK']);
			
			$rqlinsert = oci_execute($sqlinsert);

		echo ($trica['ITEM_CODE']).'<br>';
	}

exit();
echo 'Starting<br>';

$sqlinsert = oci_parse($conn, "INSERT INTO MASTER_ITEM_TEST SELECT * FROM MASTER_ITEM");
$rqlinsert = oci_execute($sqlinsert);


echo 'Ended<br>';
exit();
try{
	$bdd = new PDO('mysql:host=localhost;dbname=audit;charset=utf8', 'root', '',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e)
{
	die('erreur : '.$e->getMessage());
}


	// $tiic = $bdd->query('UPDATE count SET count = 0 WHERE id = 1');

	$sql = oci_parse($conn, "SELECT ITEM_CODE,SHOP_CODE,SUM(QTY) qty FROM HEAD_AUDIT
	WHERE audit_round='1'
	GROUP BY ITEM_CODE,SHOP_CODE
	UNION ALL
	 select distinct ITEM_CODE,SHOP_CODE,0 from  MASTER_ITEM B1
	WHERE
	item_code not in (
	select distinct item_code from HEAD_AUDIT
	 )
	AND CURR_STOCK<>0");

	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$r2 = oci_execute($sql);

	
	while ($trica = oci_fetch_array($sql)) {

		$tic = $bdd->query('UPDATE count SET count = count + 1 WHERE id = 1');

		// echo ($trica['ITEM_CODE']).'<br>';
	}

	

	



	oci_free_statement($sql);
	
	
