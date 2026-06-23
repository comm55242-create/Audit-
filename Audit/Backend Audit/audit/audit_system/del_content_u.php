<?php
ob_start();
?>
<?php require_once("includes/connection.php");?>
<?php require_once("includes/functions.php");?>

<?php
	if ($_GET['subj'] == ''){
		redirect_to("new_user.php?d=0");
	}else{
	
	$id = $_GET['subj'];
	
		$query = oci_parse($conn, "DELETE FROM USERS WHERE STAFF_ID = '{$id}'");
		$r2 = oci_execute($query);

		if($query){
			//Success
			redirect_to("new_user.php?d=1");
		} else {
			// Deletion Failed
			echo "<p>Product deletion failed.</p>";
			echo "<p>" . oci_error() . "</p>";
			echo "<a href=\"new_user.php\">Return to Main Page</p>";
		}
	}
?>
<?php
ob_end_flush();
?>