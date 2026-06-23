<?php 
// save post variables
$zone = $_POST["zone"];
$gnr_no = (int)$_POST["gnr_no"];
$item_code = $_POST["item_code"];
$qty = (int)$_POST["qty"];
$pallet = $_POST["pallet"];
$bin = $_POST["bin"];

// connect to database
define('DB_STRING', 'oci:dbname=//localhost/orcl');
define('DB_USER', 'SHOP');
define('DB_PASSWORD', 'SHOP');

try {
	$db = new PDO(DB_STRING, DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	http_response_code(500);
    echo "Unable to connect to server. Contact IT Support";
}

// insert data
try {
	$insert = $db->prepare("INSERT INTO WH_BIN_UPDATE (ZONE, GRN_NO, ITEM_CODE, QTY, PALLET, BIN) VALUES (:zone, :gnr_no, :item_code, :qty, :pallet, :bin)");

    $insert->bindParam(':zone', $zone, PDO::PARAM_STR, 100);
    $insert->bindParam(':gnr_no', $gnr_no, PDO::PARAM_STR, 100);
    $insert->bindParam(':item_code', $item_code, PDO::PARAM_STR, 100);
    $insert->bindParam(':qty', $qty, PDO::PARAM_STR, 100);
    $insert->bindParam(':pallet', $pallet, PDO::PARAM_STR, 100);
    $insert->bindParam(':bin', $bin, PDO::PARAM_STR, 100);

    if($insert->execute()) {
		echo 'Saved Successfully!';  
    }
} catch (PDOException $e) {
	http_response_code(403);
    echo "Error saving data. Duplicate record may exist.";
    // echo $e->getMessage();
}

?>