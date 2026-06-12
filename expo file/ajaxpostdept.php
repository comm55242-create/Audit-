<?php

// error_reporting(E_ERROR | E_PARSE );
// exit;
// $sc = $_POST['sc'];

// 1. Create a datbase connection DB_PASS
$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
	echo "Connection failed";
    $err = oci_error();
	trigger_error(htmlentities($err['message'], ENT_QUOTES), E_USER_ERROR);	
}


try{
	$bdd = new PDO('mysql:host=localhost;dbname=audit;charset=utf8', 'root', '',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e)
{
	die('erreur : '.$e->getMessage());
}




if(isset($_POST['initialdata'])){

	$sqlcount = oci_parse($conn, "SELECT 
		COUNT(A.ITEM_CODE)
		-- A.ITEM_CODE,A.SHOP_CODE,A. qty,B.DEPT co 
		FROM(

	SELECT ITEM_CODE,SHOP_CODE,SUM(QTY) qty FROM HEAD_AUDIT
	GROUP BY ITEM_CODE,SHOP_CODE

	UNION ALL
	 select distinct ITEM_CODE,SHOP_CODE,0 from  MASTER_ITEM B1
	WHERE
	item_code not in (
	select distinct item_code from HEAD_AUDIT
	 ) 
	AND CURR_STOCK<>0
	and dept LIKE '%ELECTRICAL APPLIANCES%'
	)A,
	(SELECT DISTINCT iTEM_CODE,DEPT FROM MASTER_ITEM) B
	WHERE A.ITEM_CODE=B.ITEM_CODE
	and dept LIKE '%ELECTRICAL APPLIANCES%'");

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
	
}


if(isset($_POST['fetchdata'])){

	$tic = $bdd->query('SELECT count FROM count WHERE id = 1');
	$ticc = $tic->fetch();

	echo $ticc['count'];
	
	
}
$notSent = [];
$notSent2 = [];

if(isset($_POST['upload'])){

	$tiic = $bdd->query('UPDATE count SET count = 0 WHERE id = 1');

	$sql = oci_parse($conn, "SELECT 
		-- COUNT(A.ITEM_CODE)
		A.ITEM_CODE,A.SHOP_CODE,A. qty,B.DEPT co FROM(

	SELECT ITEM_CODE,SHOP_CODE,SUM(QTY) qty FROM HEAD_AUDIT
	GROUP BY ITEM_CODE,SHOP_CODE

	UNION ALL
	 select distinct ITEM_CODE,SHOP_CODE,0 from  MASTER_ITEM B1
	WHERE
	item_code not in (
	select distinct item_code from HEAD_AUDIT
	 ) 
	AND CURR_STOCK<>0
	and dept LIKE '%ELECTRICAL APPLIANCES%'
	)A,
	(SELECT DISTINCT iTEM_CODE,DEPT FROM MASTER_ITEM) B
	WHERE A.ITEM_CODE=B.ITEM_CODE
	and dept LIKE '%ELECTRICAL APPLIANCES%'");

	if (!$sql) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$r2 = oci_execute($sql);

	
	while ($trica = oci_fetch_array($sql)) {

		

		$sqldel = oci_parse($conn, "delete from POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop where vc_shop_code=:shop_code AND VC_ITEM_CODE=:item_code");
		oci_bind_by_name($sqldel, ':item_code', $trica[0]);
		oci_bind_by_name($sqldel, ':shop_code', $trica[1]);
		$rsqldel = oci_execute($sqldel);

		if($rsqldel){

			$sqlinsert = oci_parse($conn, "INSERT INTO POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop (VC_ITEM_CODE,VC_SHOP_CODE,NU_QTY) VALUES(:item_code, :shop_code, :qty) ");
			oci_bind_by_name($sqlinsert, ':item_code', $trica[0]);
			oci_bind_by_name($sqlinsert, ':shop_code', $trica[1]);
			oci_bind_by_name($sqlinsert, ':qty', $trica[2]);
			$rqlinsert = oci_execute($sqlinsert);

			if($sqlinsert){
				$tic = $bdd->query('UPDATE count SET count = count + 1 WHERE id = 1');
			}else{
				array_push($notSent, $trica);
			}
		}else{

			array_push($notSent, $trica);

		}

	}

	

	foreach ($notSent as  $data) {
		$sqldel2 = oci_parse($conn, "delete from POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop where vc_shop_code=:shop_code AND VC_ITEM_CODE=:item_code");
		oci_bind_by_name($sqldel2, ':item_code', $data[0]);
		oci_bind_by_name($sqldel2, ':shop_code', $data[1]);
		$rsqldel2 = oci_execute($sqldel2);

		if($rsqldel2){

			$sqlinsert2 = oci_parse($conn, "INSERT INTO POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop (VC_ITEM_CODE,VC_SHOP_CODE,NU_QTY) VALUES(:item_code, :shop_code, :qty) ");
			oci_bind_by_name($sqlinsert2, ':item_code', $data[0]);
			oci_bind_by_name($sqlinsert2, ':shop_code', $data[1]);
			oci_bind_by_name($sqlinsert2, ':qty', $data[2]);
			$rqlinsert = oci_execute($sqlinsert2);

			if($sqlinsert2){
				$tic = $bdd->query('UPDATE count SET count = count + 1 WHERE id = 1');
			}else{
				array_push($notSent2, $data);
			}
		}else{
			array_push($notSent2, $data);
		}
	}



	oci_free_statement($sql);
	oci_free_statement($sqldel);
	oci_free_statement($sqlinsert);
	// oci_free_statement($sqldel2);
	// oci_free_statement($sqlinsert2);
	
	if(count($notSent2)){
		echo 'nerror';
		exit;
	}
}