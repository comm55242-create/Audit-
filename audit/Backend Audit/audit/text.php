<?php 

define('DB_STRING', 'oci:dbname=//localhost/orcl');
define('DB_USER', 'SHOP');
define('DB_PASSWORD', 'SHOP');

try {
	$db = new PDO(DB_STRING, DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "connected to server";
} catch (PDOException $e) {
	http_response_code(500);
    echo "Unable to connect to server. Contact IT Support";
}

?>