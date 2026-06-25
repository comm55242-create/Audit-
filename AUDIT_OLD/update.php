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
  
  // initialize an array to hold our errors
  $errors = array();

  //perform validation on the form data

  $fields_with_lengths = array('quanttity' => 10);
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
    if ($IP == '::1') {
        $IP = '127.0.0.1';
    }
    $date_auto = time();
    // Calculate the next AUDIT_ROUND for this specific item in this shop
    $round_query = "SELECT NVL(MAX(AUDIT_ROUND), 0) + 1 AS NEXT_ROUND FROM HEAD_AUDIT WHERE ITEM_CODE = '{$item_code}' AND SHOP_CODE = '{$s_code}'";
    $round_stmt = oci_parse($conn, $round_query);
    oci_execute($round_stmt);
    $round_row = oci_fetch_array($round_stmt);
    $audit_round = isset($round_row['NEXT_ROUND']) ? $round_row['NEXT_ROUND'] : 1;

    // --- MANAGER REQUIREMENT: Archive and Truncate HEAD_AUDIT before new insert ---
    $archive_stmt = oci_parse($conn, "INSERT INTO SHOP.HEAD_AUDIT_ARCHIVE SELECT * FROM SHOP.HEAD_AUDIT");
    @oci_execute($archive_stmt);
    @oci_free_statement($archive_stmt);
    
    $truncate_stmt = oci_parse($conn, "TRUNCATE TABLE SHOP.HEAD_AUDIT");
    @oci_execute($truncate_stmt);
    @oci_free_statement($truncate_stmt);
    // -----------------------------------------------------------------------------

    $query = "INSERT INTO HEAD_AUDIT (
					SHOP_CODE, ITEM_CODE, QTY, DATE_SYS, EMP_CODE, IP, USER_NAME, RACK_NUM, AUDIT_ROUND
				)VALUES(
				'{$s_code}', '{$item_code}', '{$quanttity}', '{$dtime}', '{$staff_id}', '$IP', '$user_name', '$rack_num', {$audit_round}
				)";
    
    $result = oci_parse($conn, $query);
    $exec_success = oci_execute($result, OCI_NO_AUTO_COMMIT);
    
    if ($exec_success) {
      // Force an explicit commit
      $commit_success = oci_commit($conn);
      if ($commit_success) {
          redirect_to("index.php?d=1");
      } else {
          $e = oci_error($conn);
          echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>";
          echo "<h2>COMMIT FAILED</h2>";
          echo "<p>" . htmlentities($e['message'], ENT_QUOTES) . "</p>";
          echo "<a href=\"index.php\" class='btn btn-primary'>Return to Main Page</a>";
          echo "</div></div>";
      }
    } else {
      // Audit Failed
      $e = oci_error($result);
      echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>";
      echo "<h2>Product Audit failed.</h2>";
      echo "<p>" . htmlentities($e['message'], ENT_QUOTES) . "</p>";
      echo "<p><strong>Query:</strong> " . htmlentities($query) . "</p>";
      echo "<a href=\"index.php\" class='btn btn-primary'>Return to Main Page</a>";
      echo "</div></div>";
    }
  } else {
    if (count($errors) == 1) {
      $message = "<script> 

	 var call = confirm('There was 1 error in the form. Quantity can not accept more than 10 digits (including decimals)');  
	   if(call == false){
		   window.location.replace('index.php');
		   }
		   else{
			   window.location.replace('index.php');
			   }
	   </script>";
	echo $message;
    } else {
      // Errors occurred
      $message = "There were " . count($errors) . "errors in the form.";
      echo $message;
    }
  }
}
?>
<script>
// Ensure the global loader is hidden on this page so the user can see any error messages!
if (document.getElementById('global-page-loader')) {
    document.getElementById('global-page-loader').style.display = 'none';
}
</script>
<?php

ob_end_flush();
?>