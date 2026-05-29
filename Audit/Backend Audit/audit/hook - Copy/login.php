<?php
$no_visible_elements = true;
include('header.php'); ?>

<?php

	if(logged_in()){
		redirect_to("index.php");
	}
	// START FORM PROCCESSING
	if (isset($_POST['submit'])) { // Form has been submitted.
		$errors = array();
		$username = $_POST['username'];
		$password = $_POST['password'];
		$hashed_password = sha1($password);
		$storeid = $_POST['department'];
		// clean up the form data before putting it in the database

		//echo $hashed_password;
		if ( empty($errors) ){
			// Check database to see if username and the hashed password exist there.
			//$query = "SELECT USERNAME, STAFF_ID ";
			//$query .= "FROM USERS ";
			//$query .= "WHERE USERNAME = '{$username}' ";
			//$query .= "AND HASHED_PASSWORD = '{$hashed_password}' ";
			//$query .= "LIMIT 1";
			$result_set = oci_parse($conn,"SELECT USERNAME, STAFF_ID FROM USERS WHERE USERNAME ='".$username."' AND HASHED_PASSWORD ='".$hashed_password."'");
			
			if (!$result_set) {
				$e = oci_error($conn);
				trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
			
			$r = oci_execute($result_set);
			if (!$r) {
				$e = oci_error($result_set);
				trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
			$found_user = oci_fetch_array($result_set);
			 
		
			if (oci_num_rows($result_set) == '1'){
				//username/password authenticated
				// and only 1 match
				$query2 = oci_parse($conn, "SELECT * FROM MASTER_SHOP WHERE SHOP_CODE = '".$storeid."'");
				if (!$query2) {
					$e2 = oci_error($conn);
					trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
				}
				$r2 = oci_execute($query2);
				
				if (!$r2) {
					$e2 = oci_error($query2);
					trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
				}
				$found_store = oci_fetch_array($query2);
				
				$_SESSION['storename'] = $found_store['SHOP_NAME'];
				$_SESSION['storecode'] = $found_store['SHOP_CODE'];

				$_SESSION['username'] = $found_user['USERNAME'];
				$_SESSION['staff_id'] = $found_user['STAFF_ID'];
				
			
				redirect_to("index.php");
			}else {
				// username/password combo was not found in the database
				$message = "<font color=\"#FF0033\">Username/password combination incorrect.</font><br />
					Please make sure your caps lock key is off and try again.";		
			}
		}else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				$message = "There were " . count($errors) . "errors in the form.";
			}
		}
	} else { // Form has not been submitted.
		if (isset($_GET['logout']) && $_GET['logout'] == 1) {
			$message = "You are now logged out ";
		}
		$username = "";
		$password = "";
		$storeid = "";
} 
	
?>
<div class="row">
        <div class="col-md-12 center login-header">
            <h2>Welcome To MELCOM AUDIT SYSTEM </h2>
    </div>
        <!--/span-->
    </div><!--/row-->

    <div class="row">
        <div class="well col-md-5 center login-box">
            <div class="alert alert-info">
              <?php if(!empty($message)) {echo $message;}elseif(!empty($errors)) { display_errors($errors); }else{?>
						Please login with your Username and Password.
<?php }?>
            </div>
            <form class="form-horizontal" action="login.php" method="post" >
                <fieldset>
                    <div class="input-group input-group-lg">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user red"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Username" required="required">
                    </div>
                    <div class="clearfix"></div><br>

                    <div class="input-group input-group-lg">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required="required">
                    </div>
					
 <div class="clearfix"></div><br>

<b>Select Store: </b><select class="form-control"  id="selectError" required name = "department">
								<option selected></option>
                                 <?php  $sqler = oci_parse($conn, "SELECT * FROM MASTER_SHOP WHERE ACTIVE = '1' AND AUDIT_SYS = '1'");
								   oci_execute($sqler);?>								<?php
								   while ($ritio = oci_fetch_array($sqler)){

									   ?>
                                   <option value = "<?php echo $ritio['SHOP_CODE']; ?>"><?php echo $ritio['SHOP_NAME']; ?></option>
									   
									 <?php  }
								   ?>
                                </select>
								
								
								<div class="clearfix"></div>
                     <div class="input-prepend">
                        <label class="remember" for="remember"><input type="checkbox" id="remember"> Remember me</label>
                    </div>
                    <div class="clearfix"></div>

                    <p class="center col-md-5">
                        <button type="submit" name="submit" class="btn btn-primary">Login</button>
                    </p>
                </fieldset>
                
            </form>
        </div>
        <!--/span-->
    </div><!--/row-->
<?php //require('footer.php'); ?>