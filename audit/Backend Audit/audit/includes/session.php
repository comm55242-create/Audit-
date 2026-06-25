<?php
	session_start();
	
	function logged_in() {
		return isset($_SESSION['storename']);
		return isset($_SESSION['admin']);
		return isset($_SESSION['storecode']);
		return isset($_SESSION['staff_id']);
		return isset($_SESSION['rack']);
	}
	
	function confirm_logged_in() {
		$page = $_SERVER['PHP_SELF'];
		if (!logged_in()){
			redirect_to("login.php");
		}
	}
?>