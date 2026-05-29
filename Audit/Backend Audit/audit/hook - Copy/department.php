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
    <ul class="breadcrumb">
        <li>
            <a href="#">Home</a>
        </li>
        <li>
            <a href="#">Shops</a>
        </li>
    </ul>
</div>

<div class="row">
    <div class="box col-md-12">
        <div class="box-inner">
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
			if ($e_menu){
		
$message34 = "Update Successfull";
echo $message34;
				}
		*/
			//$message = "class=\"input\"";
if(isset($_POST['edit'])){
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
			if((isset($_POST['audit'])) == 1){
				if((!empty($think)) && ($think != $s_code)){
					echo "<script> 
							alert('Activating more than one shop for auditing is not allowed on this server'); 
							  
					</script>";
					
				}else{
					$e_menu = oci_parse($conn, $trick);
					oci_execute($e_menu);
					if ($e_menu){
						$message34 = "Update Successfull";
						echo $message34;
					}
				}	
			}else{
				$e_menu = oci_parse($conn, $trick);
					oci_execute($e_menu);
					if ($e_menu){
						$message34 = "Update Successfull";
						echo $message34;
					}
			}
}
	//confirm_query($e_menu);		
				
	
?>
         <?php if(isset($_GET['subj'])){
			 $d_id = $_REQUEST['subj'];
			$sql = "SELECT * FROM MASTER_SHOP";
			$sql .= " WHERE SHOP_CODE = '{$d_id}'";
			$menu_s = oci_parse($conn, $sql);
			oci_execute($menu_s);

	  $rents = oci_fetch_array($menu_s);
	  $d_name = $rents['SHOP_NAME'];
	  
			 ?>
             <div class="box-content">
             <form action="department.php?subj=<?php echo $d_id; ?>" method="post">
						<table>
							<tr>
								<td><label>Shop name:</label></td>
								<td><input type="text" name="dname" maxlength="30"
								value="<?php echo htmlentities($d_name); ?>"  required/>
                                <input type="hidden" name="id_d" maxlength="30"
								value="<?php echo $d_id; ?>" />
                                </td>
							</tr>
                            <tr>
								<td><label>Shop Code:</label></td>
								<td><input type="text" name="s_code" maxlength="30"
								value="<?php echo htmlentities($rents['SHOP_CODE']); ?>"  required/>
                                
                                </td>
							</tr>
                            <tr>
								<td><label>Active:</label></td>
								<td><input type="checkbox" <?php if (empty($rents['ACTIVE']) ){ echo '';?> <?php }else{?> checked<?php }?>  name="active" value= 1 /></td>
							</tr>
                            <tr>
								<td><label>Audit:</label></td>
								<td><input type="checkbox" <?php if (empty($rents['AUDIT_SYS']) ){ echo '';?> <?php }else{?> checked<?php }?>  name="audit" value= 1 /></td>
							</tr>
							<tr>
								<td><button type = "submit" name="edit" value="Create Department">Edit Shop</button></td>
							</tr>
						</table>
                        </form>
             </div>
             
             <?php }else{?>
        <div class="box-content">
                    <form action="department.php" method="post">
						<table>
							<tr>
								<td><label>Shope name:</label></td>
								<td><input type="text" name="dname" maxlength="30"
								value=""  required/></td>
							</tr>
                            <tr>
								<td><label>Shope code:</label></td>
								<td><input type="text" name="s_code" maxlength="30"
								value=""  required/></td>
							</tr>
                            <noscript><tr>
								<td><label>Active:</label></td>
								<td><input type="checkbox" name="active" value="1" /></td>
							</tr>
                            <tr>
								<td><label>Audit:</label></td>
								<td><input type="checkbox" name="audit" value="1" /></td>
							</tr></noscript>
							<tr>
								<td><button name="submit" value="Create Department">Create Shop</button></td>
							</tr>
						</table>
                        </form>
					</div><?php }?>
				</div>
            <div class="box-header well" data-original-title="">
                <h2><i class="glyphicon glyphicon-list-alt"></i> Shops</h2>

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

 if($ytu['ADMIN'] == 1){ ?>							  <th>Actions</th>
	<?php }?>						  </tr>
						  </thead>   
						  <tbody>
<?php $sql = oci_parse($conn, "SELECT * FROM MASTER_SHOP
");
oci_execute($sql);
while($result = oci_fetch_array($sql)){
	$idss = $result['SHOP_CODE'];
?>      
                          
                          
							<tr>
								<td><?php echo $result['SHOP_NAME']; ?></td>
								<td class="center"><?php echo $result['SHOP_CODE']; ?></td>
								<td class="center"><?php if ($result['AUDIT_SYS'] == 1 ){?><span class="label label-success">Audit On</span>
<?php }else{?>
<span class="label label-warning">Audit Off</span>						
<?php }?>						  </td>
				</td>
		<?php
$oot = $_SESSION['staff_id'];
$add = oci_parse($conn, "SELECT * FROM USERS WHERE STAFF_ID = '{$oot}'");
oci_execute($add);
$ytu = oci_fetch_array($add);

 if($ytu['ADMIN'] == 1){ ?>						<td class="center">																<a class="btn btn-info" href="<?php $_SERVER['PHP_SELF'];?>?subj=<?php echo $idss;?>" >
										<i class="glyphicon glyphicon-edit icon-white"></i>
										Edit
									</a>
                             <a class="btn btn-danger" href="del_content_dep.php?subj=<?php echo $idss;?>" onClick="return confirm('Are you sure?');">
										<i class="glyphicon glyphicon-trash icon-white"></i>
										Delete
									</a>
								</td>
	<?php }?>						</tr>
                            <?php }?>
                            
                            
                            
                            
							
						  </tbody>
					  </table>
            </div>
        </div>
    </div>
</div><!--/row-->


<?php include('footer.php'); ?>
