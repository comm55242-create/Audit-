<?php

$conn = oci_connect('SHOP', 'SHOP', 'localhost/ORCL');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
echo " connected sucessfuly";
?>