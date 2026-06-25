<?php

$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
	echo "Connection failed";
    $err = oci_error();
	trigger_error(htmlentities($err['message'], ENT_QUOTES), E_USER_ERROR);	
}



if(isset($_POST['masteritemcount'])){

	$sc = $_POST['sc'];
	$sqlcount = oci_parse($conn, "SELECT COUNT(DISTINCT item_code) co FROM (
	select item_code,item_name,barcode,price,dept,S.NU_BALANCE_QTY,S.VC_SHOP_CODE
	from makess.vs_item_audit_final@db_link_shop a
	 left outer join
	 pos.shop_stock_summary@db_link_shop s on s.vc_item_code=a.item_code and s.vc_comp_code='01'
	and s.vc_shop_code='{$sc}')");

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
	
if(isset($_POST['loaditemmaster'])){

	$sc = $_POST['sc'];

	$sqltruncmaster = oci_parse($conn, "TRUNCATE TABLE SHOP.MASTER_ITEM");
	oci_execute($sqltruncmaster);

	$sqlinsert = oci_parse($conn, "INSERT INTO MASTER_ITEM (item_code,item_name,barcode,price,dept,CURR_STOCK,SHOP_CODE,CH_PI, CH_STATUS,VC_GROUP,VC_SUBGROUP,VC_UNIT)
	select item_code,item_name,barcode,price,dept,S.NU_BALANCE_QTY,S.VC_SHOP_CODE,A.CH_PI, A.CH_STATUS,GROUPS,SUB_GROUP,SUBSTR(TRIM(A.VC_UNIT), 1, 12)
	from makess.vs_item_audit_final@db_link_shop a
 	left outer join pos.shop_stock_summary@db_link_shop s on s.vc_item_code=a.item_code and s.vc_comp_code='01'
	and s.vc_shop_code='{$sc}'");

	if (!$sqlinsert) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	oci_execute($sqlinsert);


	oci_free_statement($sqlinsert);
	oci_free_statement($sqltruncmaster);


	echo 'ok';	
}

if(isset($_POST['loadingdeptwise'])){

	$sc = $_POST['sc'];

	$deptQuery = "";
	$deptCodeArray = $_POST['depts'];

	$str = '';
	for($i = 0; $i < count($deptCodeArray); $i++){
		
		if($i == 0){
			$str = $str."'".$deptCodeArray[$i]."'";
		}else{
			$str = $str.",'".$deptCodeArray[$i]."'";
		}
	}

	$deptQuery = "WHERE DEPT IN (".$str.")";

	$sqltruncmaster = oci_parse($conn, "TRUNCATE TABLE SHOP.MASTER_ITEM");
	oci_execute($sqltruncmaster);

	$sqlinsert = oci_parse($conn, "
		INSERT INTO MASTER_ITEM (item_code,item_name,barcode,price,dept,CURR_STOCK,SHOP_CODE,CH_PI, CH_STATUS,VC_GROUP,VC_SUBGROUP,VC_UNIT)
		SELECT * FROM 
	(select item_code,item_name,barcode,price,dept,S.NU_BALANCE_QTY,S.VC_SHOP_CODE,A.CH_PI, A.CH_STATUS,GROUPS,SUB_GROUP,SUBSTR(TRIM(A.VC_UNIT), 1, 12)
		from makess.vs_item_audit_final@db_link_shop a
	 	left outer join pos.shop_stock_summary@db_link_shop s on s.vc_item_code=a.item_code and s.vc_comp_code='01'
		and s.vc_shop_code='{$sc}'
	) 
	".$deptQuery."");

	if (!$sqlinsert) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	oci_execute($sqlinsert);


	oci_free_statement($sqlinsert);
	oci_free_statement($sqltruncmaster);


	echo 'ok';	
}

if(isset($_POST['loadinggroupwise'])){

	$sc = $_POST['sc'];

	$groupQuery = "";
	$groupCodeArray = $_POST['groups'];

	$str = '';
	for($i = 0; $i < count($groupCodeArray); $i++){
		
		if($i == 0){
			$str = $str."'".$groupCodeArray[$i]."'";
		}else{
			$str = $str.",'".$groupCodeArray[$i]."'";
		}
	}

	$groupQuery = "WHERE GROUPS IN (".$str.")";

	$sqltruncmaster = oci_parse($conn, "TRUNCATE TABLE SHOP.MASTER_ITEM");
	oci_execute($sqltruncmaster);

	$sqlinsert = oci_parse($conn, "
		INSERT INTO MASTER_ITEM (item_code,item_name,barcode,price,dept,CURR_STOCK,SHOP_CODE,CH_PI, CH_STATUS,VC_GROUP,VC_SUBGROUP,VC_UNIT)
		SELECT * FROM 
	(select item_code,item_name,barcode,price,dept,S.NU_BALANCE_QTY,S.VC_SHOP_CODE,A.CH_PI, A.CH_STATUS,GROUPS,SUB_GROUP,SUBSTR(TRIM(A.VC_UNIT), 1, 12)
		from makess.vs_item_audit_final@db_link_shop a
	 	left outer join pos.shop_stock_summary@db_link_shop s on s.vc_item_code=a.item_code and s.vc_comp_code='01'
		and s.vc_shop_code='{$sc}'
	) 
	".$groupQuery."");

	if (!$sqlinsert) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	oci_execute($sqlinsert);


	oci_free_statement($sqlinsert);
	oci_free_statement($sqltruncmaster);


	echo 'ok';	
}

if(isset($_POST['loadingsubgroupwise'])){

	$sc = $_POST['sc'];

	$subgroupQuery = "";
	$subgroupCodeArray = $_POST['subgroups'];

	$str = '';
	for($i = 0; $i < count($subgroupCodeArray); $i++){
		
		if($i == 0){
			$str = $str."'".$subgroupCodeArray[$i]."'";
		}else{
			$str = $str.",'".$subgroupCodeArray[$i]."'";
		}
	}

	$subgroupQuery = "WHERE SUB_GROUP IN (".$str.")";

	$sqltruncmaster = oci_parse($conn, "TRUNCATE TABLE SHOP.MASTER_ITEM");
	oci_execute($sqltruncmaster);

	$sqlinsert = oci_parse($conn, "
		INSERT INTO MASTER_ITEM (item_code,item_name,barcode,price,dept,CURR_STOCK,SHOP_CODE,CH_PI, CH_STATUS,VC_GROUP,VC_SUBGROUP,VC_UNIT)
		SELECT * FROM 
	(select item_code,item_name,barcode,price,dept,S.NU_BALANCE_QTY,S.VC_SHOP_CODE,A.CH_PI, A.CH_STATUS,GROUPS,SUB_GROUP,SUBSTR(TRIM(A.VC_UNIT), 1, 12)
		from makess.vs_item_audit_final@db_link_shop a
	 	left outer join pos.shop_stock_summary@db_link_shop s on s.vc_item_code=a.item_code and s.vc_comp_code='01'
		and s.vc_shop_code='{$sc}'
	) 
	".$subgroupQuery."");

	if (!$sqlinsert) {
		$e2 = oci_error($conn);
		trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
	}
	oci_execute($sqlinsert);


	oci_free_statement($sqlinsert);
	oci_free_statement($sqltruncmaster);


	echo 'ok';	
}


if(isset($_POST['refreshitemmaster'])){

	$sqlinsert = oci_parse($conn, "insert into SHOP.HEAD_AUDIT_ARCHIVE select * from SHOP.HEAD_AUDIT");
	oci_execute($sqlinsert);

	$sqltrunchead = oci_parse($conn, "truncate table SHOP.HEAD_AUDIT");
	oci_execute($sqltrunchead);

	$sqltruncmaster = oci_parse($conn, "TRUNCATE TABLE SHOP.MASTER_ITEM");
	oci_execute($sqltruncmaster);

	$sqlcommit = oci_parse($conn, "COMMIT");
	oci_execute($sqlcommit);

	oci_free_statement($sqlinsert);
	oci_free_statement($sqltrunchead);
	oci_free_statement($sqltruncmaster);
	oci_free_statement($sqlcommit);

	echo 'ok';		
}


if(isset($_POST['initialdata'])){

	$sqlcount = oci_parse($conn, "SELECT COUNT(VC_ITEM_CODE) co FROM ZS_STOCK_AUDIT_ERP_NEW");

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

	$sc = $_POST['sc'];

	$sqlcount = oci_parse($conn, "select COUNT(VC_ITEM_CODE) sc FROM  POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop WHERE vc_shop_code='{$sc}' ");

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

if(isset($_POST['uploadtoerp'])){
	$sc = $_POST['sc'];
	$sql = oci_parse($conn, "BEGIN PRC_STOCK_ADJUSTMENT_BULK(:p_shop_code); END;");
	oci_bind_by_name($sql, ':p_shop_code', $sc);
	$result = oci_execute($sql);
	
	if ($result) {
		echo "success";
	} else {
		$e = oci_error($sql);
		echo htmlentities($e['message'], ENT_QUOTES);
	}
	oci_free_statement($sql);
}

