<?php include('header.php'); ?><?php confirm_logged_in(); ?>
<?php
	// START FORM PROCCESSING
	if (isset($_POST['submit'])) { // Form has been submitted.
		// initialize an array to hold our errors
		/*$errors = array();
		
		// perform validation on the form data
		$required_fields = array('dname');
		$errors = array_merge($errors, check_max_field_lengths($required_fields));
		
		$fields_with_lengths = array('dname' => 30);
		$errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));
		*/
		// clean up the form data before putting it in the database
		$dname = trim($_POST['dname']);
		$s_code = trim($_POST['s_code']);
		//$active = trim($_POST['active']);
		//$audit = trim($_POST['audit']);
		
	
		if (empty($errors)){
			$query = "INSERT INTO MASTER_SHOP (SHOP_CODE, SHOP_NAME, ACTIVE, AUDIT_SYS
				)VALUES('{$s_code}', '{$dname}', '0', '0')";
			$result = oci_parse($conn, $query);
			
			$r2 = oci_execute($result);
			if ($result){
				$message = "The Store was successfully created.";
			} else {
				//Failed
				$message = "The Store could not be created.";
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
		$dname = "";
	}
?>

<div>
    <ol class="breadcrumb">
		<li><a href="#">Home</a></li>
		<li><a href="#">Shops</a></li>
    </ol>
</div>

<div class="row">
    <div class="box col-md-12">
        <div class="pannel panel-default">
		<div class="box-header well" data-original-title=""><i class="glyphicon glyphicon-cog"></i> Manage Shop
		<div class="box-icon">
		<a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
		<a href="#" class="btn btn-close btn-round btn-danger"><i class="glyphicon glyphicon-remove"></i></a>
		</div></div>
		<div class="box-icon">
		</div>
        <?PHP 
		
		/*echo "<script> 
		alert('Activatin more than one shop for Auditing is not allowed on this server'); 
		</script>";
		$trick = "UPDATE MASTER_SHOP SET ";
		$trick .= "SHOP_CODE = '{$s_code}', ";
		$trick .= "SHOP_NAME = '{$dname}', ";
		$trick .= "ACTIVE = '{$active}', ";
		$trick .= "AUDIT_SYS = '{$audit}'";
		$trick .= " WHERE SHOP_CODE = '{$id_d}'";
		$e_menu = oci_parse($conn, $trick);
		oci_execute($e_menu);
		if ($e_menu)
		{
			$message34 = "Update Successfull";
			echo $message34;
		}
		*/
			//$message = "class=\"input\"";
			if(isset($_POST['edit']))
			{
				error_reporting(0);
				$dname = $_POST['dname'];
				$id_d = $_POST['id_d'];
				$s_code = $_POST['s_code'];
				$active = intval($_POST['active']);
				$audit = intval($_POST['audit']);
				$power = oci_parse($conn, "SELECT * FROM MASTER_SHOP WHERE AUDIT_SYS = '1'");
				$trick = "UPDATE MASTER_SHOP SET ";
				$trick .= "SHOP_CODE = '{$s_code}', ";
				$trick .= "SHOP_NAME = '{$dname}', ";
				$trick .= "ACTIVE = '{$active}', ";
				$trick .= "AUDIT_SYS = '{$audit}'";
				$trick .= " WHERE SHOP_CODE = '{$id_d}'";
			
				oci_execute($power);
				$dread = oci_fetch_array($power);
				$think = $dread['SHOP_CODE'];
				if((isset($_POST['audit'])) == 1)
				{
					if((!empty($think)) && ($think != $s_code))
					{
						echo "<script> alert('Activating more than one shop for auditing is not allowed on this server');</script>";
					}else
					{
						$e_menu = oci_parse($conn, $trick);
						oci_execute($e_menu);
						if ($e_menu)
						{
							$message34 = "<div class='alert alert-success' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='close'><span area-hidden='true'>&times;</span></button><span class='glyphicon glyphicon-ok-circle'></span> Shop Status Modified <strong>Successfully!!</strong></div>";
							echo $message34;
						}
					}	
				}else{
					$e_menu = oci_parse($conn, $trick);
						oci_execute($e_menu);
						if ($e_menu)
						{
							$message34 = "<div class='alert alert-success' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='close'><span area-hidden='true'>&times;</span></button><span class='glyphicon glyphicon-ok-circle'></span> Shop Status Modified <strong>Successfully!!</strong></div>";
							echo $message34;
						}
				}
			} //POST
	//confirm_query($e_menu);
?>
		<?php if(isset($_GET['subj']))
		{
			$d_id = $_REQUEST['subj'];
			$sql = "SELECT * FROM MASTER_SHOP";
			$sql .= " WHERE SHOP_CODE = '{$d_id}'";
			$menu_s = oci_parse($conn, $sql);
			oci_execute($menu_s);
			$rents = oci_fetch_array($menu_s);
			$d_name = $rents['SHOP_NAME'];
		?>
            <div class="box-content box innner">
				<form action="department.php?subj=<?php echo $d_id; ?>" method="post" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label">Shop Name:</label>
						<div class="col-sm-3">
						<input type="text" name="dname" maxlength="30" class ="form-control" value="<?php echo htmlentities($d_name); ?>"  required/>
						</div><div class="col-sm-7"></div>
                            <input type="hidden" name="id_d" maxlength="30" class ="form-control" value="<?php echo $d_id; ?>" />
                        </td>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Shop Code:</label>
						<div class="col-sm-3">
						<td><input type="text" name="s_code" maxlength="30" class ="form-control" value="<?php echo htmlentities($rents['SHOP_CODE']); ?>"  required/>
                        </div><div class="col-sm-7"></div>
						</td>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Active:</label>
						<div class="col-sm-3">
						<input type="checkbox" <?php if (empty($rents['ACTIVE']) ){ echo '';?> <?php }else{?> checked<?php }?>  name="active" value= 1 />
						</div><div class="col-sm-7"></div>
					</div>
                            <!--<tr>
								<td><label>Audit:</label></td>
								<td><input type="number" name="audit" value= <?php echo htmlentities($audit); ?> MIN ="1" MAX = "9" <?php if (empty($rents['AUDIT_SYS']) ){ echo '';?> <?php }else{?> checked<?php }?> /></td>
							</tr>
							<tr>
							?!-->
							
					<div class="form-group">
						<label class="col-sm-2 control-label">Audit Round:</label>
						<div class="col-sm-3"><input type="text" name="audit" maxlength="10" class ="form-control"
						value="<?php echo htmlentities($rents['AUDIT_SYS']); ?>"  required/>
						</div>
						<div class="col-sm-7"></div>
					</div>
					<div class="form-group">
						<div class="col-sm-2"></div>
						<div class="col-sm-10"><button class="btn btn-primary" type = "submit" name="edit" value="Create Department">Edit Shop</button></div>
					<br>
				</form>
			</div>
             
             <?php }else{?>
		
        <div class="box-content box innner">
			<form action="department.php" method="post" class="form-horizontal">
				<div class="form-group">
					<label class="control-label col-sm-2">Shop Name:</label>
					<div class="col-sm-3"><input type="text" name="dname" maxlength="30" class="form-control" value=""  required/></div>
					<div class="col-sm-7"></div>
                </div>
				<div class="form-group">
					<label class="control-label col-sm-2">Shop Code:</label>
					<div class="col-sm-3"><input type="text" name="s_code" maxlength="30" class="form-control" value=""  required/></div>
					<div class="col-sm-7"></div>
				</div>
                    <noscript>
					<div class="form-group">
						<label class="control-label col-sm-2">Active:</label>
						<div class="col-sm-3"><input type="checkbox" name="active" value="1" /></div>
						<div class="col-sm-7"></div>
					</div>
                    <div class="form-group">
						<label class="control-label col-sm-2">Audit:</label>
						<div class="col-sm-2"><input type="checkbox" name="audit" value="1" /></div>
					</div>
					</noscript>
					<div class="form-group">
						<div class="col-sm-2"></div>
						<div class="col-sm-2"><button name="submit" class="btn btn-primary" value="Create Department">Create Shop</button></div>
						<div class="col-sm-8"></div>
					</div>
				
            </form>
		</div>
		</div>
	</div><!-- col-md-12-->
		<?php }?>
</div>
		<br>
		<div class="panel panel-default"><!-- pannel default-->
        <div class="box-header well" data-original-title="">
        <h2><i class="glyphicon glyphicon-shopping-cart"></i> Shops</h2>
            <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
                <a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
            </div>
        </div>
            
        <div class="box-content">
            <div class="box-content alerts">
			<?php if(isset($_GET['d']) == "1") {?>
				<div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Success!</strong><p>Shop Deleted !!</p>
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
            <table id="example" class="display table table-striped table-bordered bootstrap-datatable datatable">
				<thead>
					<tr>
						<th>Shops</th>
						<th>Shop Code</th>
						<th>Status</th>
			<?php
				$oot = $_SESSION['staff_id'];
				$add = oci_parse($conn, "SELECT ADMIN FROM USERS WHERE STAFF_ID = '{$oot}'");
				oci_execute($add);
				$ytu = oci_fetch_array($add);
				if($ytu['ADMIN'] == 1){ 
			?> 
						<th>Actions</th>
			<?php }?>
					</tr>
				</thead>   
			<tbody>
			<?php $sql = oci_parse($conn, "SELECT * FROM MASTER_SHOP");
				oci_execute($sql);
				while($result = oci_fetch_array($sql)){
				$idss = $result['SHOP_CODE'];
			?>          
				<tr>
					<td><?php echo $result['SHOP_NAME']; ?></td>
					<td class="center"><?php echo $result['SHOP_CODE']; ?></td>
					<td class="center"><?php if ($result['AUDIT_SYS'] == 1 ){?><span class="label label-success">Audit On</span>
<?php }else{?>
<span class="label label-danger">Audit Off</span>						
<?php }?>			</td>
				</td>
		<?php
$oot = $_SESSION['staff_id'];
$add = oci_parse($conn, "SELECT * FROM USERS WHERE STAFF_ID = '{$oot}'");
oci_execute($add);
$ytu = oci_fetch_array($add);

 if($ytu['ADMIN'] == 1){ ?>						<td class="center"> <a class="btn btn-xs btn-info" href="<?php $_SERVER['PHP_SELF'];?>?subj=<?php echo $idss;?>" >
										<i class="glyphicon glyphicon-edit icon-white"></i> Edit</a>
                             <a class="btn btn-xs btn-danger" href="del_content_dep.php?subj=<?php echo $idss;?>" onClick="return confirm('Are you Sure you Want to\n Delete Shop ?');">
										<i class="glyphicon glyphicon-trash icon-white"></i> Delete</a>
								</td>
	<?php }?>						</tr>
                            <?php }?>
                            
                            
                            
                            
							
					</tbody>
				</table>
			</div>
        </div>
    </div></div>
</div><!--/row-->


<?php include('footer.php'); ?>
