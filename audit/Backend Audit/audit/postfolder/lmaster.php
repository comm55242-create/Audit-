<?php

$conn=oci_connect('SHOP','SHOP','(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521)) (CONNECT_DATA = (SERVICE_NAME = orcl) (SID = orcl)))');
if(!$conn){
	echo "Connection failed";
    $err = oci_error();
	trigger_error(htmlentities($err['message'], ENT_QUOTES), E_USER_ERROR);	
}

// 'LFS','SPN','ACH','KAS','MSS','M04','TMP','M03','NAN','KCS'

	$sql = oci_parse($conn, "
		SELECT * FROM
(
select DISTINCT item_code,item_name, price,dept , NU_BALANCE_QTY, VC_SHOP_CODE
		from makess.vs_item_audit_final@db_link_shop a
	 	left outer join pos.shop_stock_summary@db_link_shop s on s.vc_item_code=a.item_code and s.vc_comp_code='01'
		and s.vc_shop_code IN ('LFS','SPN','ACH','TMP','MSS','M03','M04','M05','KAS','KCS','KS7','HAA','ADB','MDN','MAS','GBA','FAR')
)
WHERE  NU_BALANCE_QTY <>0 AND NU_BALANCE_QTY IS NOT NULL");

	oci_execute($sql);

	$r2 = oci_execute($sql);
	$dtime = date('Y-m-d_H-i-s');
	$name = 'Stock_Query_'.$dtime.'.csv';
	$dron2 = '../export/'.$name;
	$fp = fopen($dron2, 'w');

	fputcsv($fp, ['SHOP_CODE','ITEM_CODE','ITEM_NAME','PRICE','ERP_QTY','DEPT']);

	while ($trica = oci_fetch_array($sql)) {

		// $ITEM_CODE = $trica['ITEM_CODE'];
		// $ITEM_NAME = $trica['ITEM_NAME'];
		// $ERP_QTY = $trica['NU_BALANCE_QTY'];
		// $DEPT = $trica['DEPT'];
		// $PRICE = $trica['PRICE'];

		$data['VC_SHOP_CODE'] = $trica['VC_SHOP_CODE'];
		$data['ITEM_CODE'] = $trica['ITEM_CODE'];
		$data['ITEM_NAME'] = (string)$trica['ITEM_NAME'];
		$data['PRICE'] = $trica['PRICE'];
		$data['NU_BALANCE_QTY'] = $trica['NU_BALANCE_QTY'];
		$data['DEPT'] = $trica['DEPT'];
		
		

		fputcsv($fp, $data);

		// echo $ITEM_CODE.' - '.$ITEM_NAME.' - '.$PRICE.' - '.$ERP_QTY.' - '.$DEPT;
		// echo '<br>';
	}



?>


<a href="<?= $dron2 ?>">Download</a>