<?php
require_once '../../conn.php';

$shop_code = $_POST['shop_code'];
$item_code = $_POST['item_code'];
$qty       = $_POST['qty'];
$user      = $_POST['user'];
$ip        = $_POST['ip'];
$rack      = $_POST['rack'];

try {
    $db->exec("INSERT INTO head_audit_archive SELECT * FROM head_audit");
    $db->exec("TRUNCATE TABLE head_audit");
} catch (Exception $e) { }

$query = $db->prepare("INSERT INTO head_audit (shop_code,item_code,qty,emp_code,IP,user_name,rack_num) VALUES ('$shop_code','$item_code','$qty','$user','$ip','$user','$rack')");
$query->execute();

echo "Sucess";
?>
