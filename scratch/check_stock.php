<?php
require 'config/Database.php';
$c = Database::getConnection();
$s = oci_parse($c, 'SELECT SUM(CURR_STOCK) AS SQTY, SUM(NVL(PRICE,0)*NVL(CURR_STOCK,0)) AS SVAL FROM SHOP.MASTER_ITEM');
oci_execute($s);
$r = oci_fetch_array($s, OCI_ASSOC);
echo json_encode($r);
?>
