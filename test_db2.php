<?php
require __DIR__ . '/config/Database.php';
$c = Database::getConnection();
$s = oci_parse($c, 'SELECT SUM(CURR_STOCK) AS SQTY, SUM(NVL(PRICE,0)*NVL(CURR_STOCK,0)) AS SVAL, SUM(PRICE) AS SPRICE FROM SHOP.MASTER_ITEM');
oci_execute($s);
print_r(oci_fetch_array($s, OCI_ASSOC));
?>
