<?php
require_once '../../conn.php';

$username = $_POST['username'];
$rack_num = $_POST['rack_num'];

$sth = $db->prepare("SELECT item_code,item_name1 item_name,qty FROM ZS_VW_AUDIT_REPORT_mob WHERE upper(user_name)=upper('$username') and upper(rack_num)=upper('$rack_num') ");
$sth->execute();

$result = $sth->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
print(json_encode($result));
?>
