<?php
require_once("includes/session.php");
require_once("includes/connection.php");
confirm_logged_in();

$sql = oci_parse($conn, "SELECT COUNT(*) AS STOCK_CNT FROM ZS_VW_AUDIT_PENDING");
oci_execute($sql);
oci_fetch($sql);
$count_num = oci_result($sql, 'STOCK_CNT');

echo json_encode(['count' => $count_num]);
?>
