<?php include('header.php'); ?><?php confirm_logged_in(); ?>
<?php
	// START FORM PROCCESSING
	if (isset($_POST['submit'])) { // Form has been submitted.
		// initialize an array to hold our errors
		/*$errors = array();
		
		// perform validation on the form data
		$required_fields = array('username', 'password');
		$errors = array_merge($errors, check_max_field_lengths($required_fields));
		
		$fields_with_lengths = array('username' => 30, 'password' => 30);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));
		
		*/// clean up the form data before putting it in the database
		$username = $_POST['username'];
		$password = $_POST['password'];
		$fullname = $_POST['fullname'];
		$staffid =  $_POST['staffid'];
		if(isset($_POST['admin']) == ''){
			$admin = 0;
		}else{
			$admin = $_POST['admin'];
		}
		$dtime = date('Y-m-d H:i:s');
		$hashed_password = sha1($password);
	
		if (empty($errors)){
			$query = "INSERT INTO USERS (
					USERNAME, E_NAME, HASHED_PASSWORD, ADMIN, DATE_SYS, STAFF_ID
				)VALUES(
					'{$username}', '{$fullname}', '{$hashed_password}', '{$admin}', '{$dtime}', '{$staffid}'
				)";
			$result = oci_parse($conn, $query);
			oci_execute($result);
			if ($result){
				$message = "The user was successfully created.";
			} else {
				//Failed
				$message = "The user could not be created.";
				$message .= "<br />". oci_error();
			}
		} else {
			if (count($errors) == 1) {
				$message = "There was 1 error in the form.";
			} else {
				// Errors occurred
				$message = "There were " . count($errors) . "errors in the form.";
			}
		}
	} else { // Form has not been submitted.
		$fullname = "";
		$staffid = "";
		$username = "";
		$password = "";
	}
?>

<div>
    <ul class="breadcrumb">
        <li>
            <a href="#">Home</a>
        </li>
        <li>
            <a href="#">User Manager</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="box col-md-12">
        <div class="box-inner">
        <div class="box-content">
                    <form action="new_user.php" method="post">
						<table>
                        <tr>
								<td><label>Full Name:</label></td>
								<td><input type="text" name="fullname" maxlength="30"
								value="<?php echo htmlentities($fullname); ?>"  required/></td>
							</tr>
							<tr>
								<td><label>User name:</label></td>
								<td><input type="text" name="username" maxlength="30"
								value="<?php echo htmlentities($username); ?>"  required/></td>
							</tr>
                            <tr>
								<td><label>Staff Id:</label></td>
								<td><input type="text" name="staffid" maxlength="30"
								value="<?php echo htmlentities($staffid); ?>"  required/></td>
							</tr>
							<tr>
								<td><label>Password:</label></td>
								<td><input type="password" name="password" maxlength="30"
								value="<?php echo htmlentities($password); ?>"  required/></td>
							</tr>
                            <tr>
								<td>Administrator:</td>
								<td><input type="checkbox" name="admin" value="1" /></td>
							</tr>
							<tr>
								<td><input type="submit" name="submit" value="Create user"></td>
							</tr>
						</table>
                        </form>
					</div>
				</div>
            <div class="box-header well" data-original-title="">
                <h2><i class="glyphicon glyphicon-user"></i> Users</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round btn-default"><i
                            class="glyphicon glyphicon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round btn-default"><i
                            class="glyphicon glyphicon-remove"></i></a>
                </div>
            </div>
            <div class="box-content">
            <div class="box-content alerts">
            <?php if(isset($_GET['d']) == "1") {?>
            <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong>
					<p>User Deleted</p>
                </div>
                <?php } ?>
			<?php if(!empty($message)) {?>
            <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                   <strong>Success!</strong>
					<p><?php echo $message;?></p>
                </div>
                <?php } ?>
            <?php if(!empty($errors)) { ?>
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Warning!</strong>
					<p><?php display_errors($errors);?></p>
                </div>
<?php }?>
            <table class="table table-striped table-bordered bootstrap-datatable datatable">
						  <thead>
							  <tr>
								  <th>Username</th>
								  <th>Date registered</th>
								  <th>Role</th>
								  <th>Actions</th>
							  </tr>
						  </thead>   
						  <tbody>
<?php $sql = oci_parse($conn, "SELECT * FROM USERS");

oci_execute($sql);

while($result = oci_fetch_array($sql)){
	$idss = $result['STAFF_ID'];
?>      
                          
                          
							<tr>
								<td><?php echo $result['USERNAME']; ?></td>
								<td class="center"><?php echo $result['DATE_SYS']; ?></td>
								<td class="center">
                                <?php if($result['ADMIN'] == 1){?>
									Administrator
								<?php }else{?>
                                	User
                                <?php }?>
                                </td>
								<td class="center">																<a class="btn btn-danger" href="del_content_u.php?subj=<?php echo $idss;?>" onClick="return confirm('Are you sure?');">
										<i class="icon-trash icon-white"></i> 
										Delete
									</a>
								</td>
							</tr>
                            <?php }?>
                            
                            
                            
                            
							
						  </tbody>
					  </table>
            </div>
        </div>
    </div>
</div><!--/row-->


<?php include('footer.php'); ?>
