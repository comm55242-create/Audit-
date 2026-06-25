<?php
require_once '../../conn.php';

$item_code1 = $_POST['item_code'];

$sth = $db->prepare("SELECT item_code,item_name,barcode,price,dept,shop_code,curr_stock, vc_unit as UNIT FROM master_item WHERE item_code='$item_code1' OR barcode='$item_code1' ");
$sth->execute();

$result = $sth->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
print(json_encode($result));
?>
