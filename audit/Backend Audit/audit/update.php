<?php

ob_start();
?>
<?php require_once("includes/connection.php"); ?>
<?php

require_once("includes/functions.php");
include_once("includes/form_functions.php");

?>
<?php include('header.php'); ?><?php confirm_logged_in(); ?>
<?php

$user_name = $_GET['un'];
if (isset($_POST['save'])) {
  
  $_SESSION['rack_number'] = $_POST['rack_number'];
  $rack_num = $_POST['rack_number'];
  $SHOP_CODE = $_SESSION['storecode'];
  $rack = $_SESSION['rack'];
  
  // initialize an array to hold our errors
  $errors = array();

  //perform validation on the form data

  $fields_with_lengths = array('quanttity' => 5);
  $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));

  //clean up the form data before putting it in the database


  if (empty($errors)) {

    $item_code = $_POST['item_code'];
    $quanttity = $_POST['quanttity'];
    $s_code = $_POST['s_code'];
    $staff_id = $_POST['staff_id'];
    $dtime = date('Y-m-d H:i:s');
    $this_page = $_SERVER['PHP_SELF'];
    $IP = $_SERVER["REMOTE_ADDR"];
    $date_auto = time();
    $query = "INSERT INTO HEAD_AUDIT (
					SHOP_CODE, ITEM_CODE, QTY, DATE_SYS, EMP_CODE, IP, USER_NAME, RACK_NUM
				)VALUES(
				'{$s_code}', '{$item_code}', '{$quanttity}', '{$dtime}', '{$staff_id}', '$IP', '$user_name', '$rack'
				)";
    $result = oci_parse($conn, $query);
    oci_execute($result);
    if ($result) {
      //Success
      redirect_to("index.php?d=1");
    } else {
      // Audit Failed
      echo "<p>Product Audit failed.</p>";
      echo "<p>" . oci_error() . "</p>";
      echo "<a href=\"index.php\">Return to Main Page</p>";
    }
  } else {
    if (count($errors) == 1) {
      $message = "<script> 

	 var call = confirm('There was 1 error in the form. Quantity can not accept more than 5 digits ');  
	   if(call == false){
		   window.location.replace('index.php');
		   }
    </script>";
    } else {
      // Errors occurred
      $message = "There were " . count($errors) . "errors in the form.";
    }
  }echo $message;
}
?>
<?php

ob_end_flush();
?>