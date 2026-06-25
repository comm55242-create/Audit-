<?php include('header.php'); ?>
<?php
confirm_logged_in();
$SHOP_CODE = $_SESSION['storecode'];
$rack_id = $_SESSION['rack_number'];
?>

<div>
  <ol class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li><a href="#">Audit Activity</a></li>
  </ol>
</div>

<div id="content" class="col-lg-12">
  <!-- content starts -->
	<div class="box-inner">
		<div class="box-header well">
			<h2 style="font-size: 13px;"><i class="glyphicon glyphicon-info-sign"></i>  Audit Activity on: <?php echo $_SESSION['storecode']; ?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round btn-default"><i class="glyphicon glyphicon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round btn-default"><i class="glyphicon glyphicon-remove"></i></a>
			</div>
		</div>
		<div class="box-content row"> 
      <?php
      $hmm = "autofocus";
      if (!empty($_POST['barcode'])) {
        $hmm = '';
      }
      ?>

		<form class="form-horizontal container" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" name="frm1">
			<br>
			<div class="form-group">
			<label class="col-sm-2 control-label">Rack Number:</label>
			<div class="col-sm-3">
			<input type="text" name="rack_number" class="form-control" value="<?php echo $rack_id; ?>" required="">
			</div>
			<div class="col-sm-7"></div>
			</div>
			<div class="form-group">
			<label class="col-sm-2 control-label">Item Bar Code:</label>
			<div class="col-sm-3">
			<input type="text" class="form-control" name="barcode" <?php echo $hmm; ?>>
			</div>
			<div class="col-sm-7"></div>
			</div><!--form-group -->
			<div class="from-group">
			<div class="col-sm-2">
				<input type="submit" class="btn btn-primary pull-right" id="box" name="submit"/>
			</div>
			<div class="col-sm-10">
			<input type="reset" class="btn btn-warning pull-left" value="Cancle" />
			</div>
			</div><!-- from group-->
			<!--<table>
				<tr>
					<td><label>Rack Number:</label></td> <td><input type="text" name="rack_number" class="form-control" value="<?php echo $rack_id; ?>" required=""></td>
				</tr>
				<tr>
					<td><label>Item Bar Code:</label></td> <td><input type="text" class="form-control" name="barcode" <?php echo $hmm; ?>></td>
				</tr>
					<!--<tr>
					<td>Item Code:</td><td> <input type="text" name="item_code"></td>
					</tr>--
				<tr>
				<td>
					<input type="submit" class="btn btn-primary" id="box" name="submit"/></td><td><input type="reset" class="btn btn-warning" value="Cancle" /></td>
				</tr>
			</table>-->
		</form>

      <?php
      if (isset($_POST['submit'])) {
        
        $_SESSION['rack_number'] = $_POST['rack_number'];
        $txtbarcode = $_POST['barcode'];
		//$txtitcode=$_POST['item_code'];

        $result = oci_parse($conn, "SELECT DISTINCT BARCODE, ITEM_CODE, ITEM_NAME, PRICE FROM MASTER_ITEM WHERE BARCODE = UPPER('{$txtbarcode}') ");
        if (!$result) {
          $e = oci_error($conn);
          trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $r = oci_execute($result);

        $toto = oci_fetch_array($result);
        $barcode = $toto['BARCODE'];
        $item_code = $toto['ITEM_CODE'];
        $i_des = $toto['ITEM_NAME'];
        $i_price = $toto['PRICE'];

        $sql = oci_parse($conn, "SELECT ITEM_CODE, SUM(QTY) QTY, DATE_SYS, EMP_CODE FROM HEAD_AUDIT WHERE ITEM_CODE = '{$item_code}' AND SHOP_CODE = '{$SHOP_CODE}' AND QTY <> 0 GROUP BY ITEM_CODE, DATE_SYS, EMP_CODE");
        if (!$sql) {
          $e2 = oci_error($conn);
          trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $r2 = oci_execute($sql);

        $trica = oci_fetch_array($sql);
        $tinto = $trica['ITEM_CODE'];
        $Qty = $trica['QTY'];
        $Date = $trica['DATE_SYS'];

        $emp = $trica['EMP_CODE'];
        $sql2 = oci_parse($conn, "SELECT E_NAME FROM USERS WHERE STAFF_ID = '{$emp}'");
        if (!$sql2) {
          $e3 = oci_error($conn);
          trigger_error(htmlentities($e3['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $r3 = oci_execute($sql2);

        $ben = oci_fetch_array($sql2);
        $e_name2 = $ben['E_NAME'];
        if ($i_des == '') {
          ?>
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>Error!</strong> No Record Found
          </div> 

        <?php
        } else {

          if ($item_code == $tinto) {
            ?>
            <div class="alert alert-warning" style="margin-bottom: 15px; padding: 10px 15px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: 4px; font-size: 13px;">
              <button type="button" class="close" data-dismiss="alert" style="float: right; font-size: 18px; font-weight: bold; line-height: 1; color: #000; text-shadow: 0 1px 0 #fff; opacity: .2; border: none; background: transparent;">&times;</button>
              <h5 style="margin-top: 0; margin-bottom: 8px; font-size: 14px; font-weight: bold;"><i class="glyphicon glyphicon-warning-sign"></i> Duplicate Entry Blocked!</h5>
              <div style="line-height: 1.4;">
                This item has <strong>already been entered</strong>.<br>
                Item Code <strong><?php echo $tinto; ?></strong> was entered twice.
              </div>
            </div>
            <?php
          } else {
          ?>
		<div class="container-fluid">
			<form action="update.php?un=<?php echo $_SESSION['username']; ?>&rn=<?php echo $_SESSION['rack_number'];?>" method="post" class="form-horizontal container">
			<div class="form-group">
				<input type="hidden" name="s_code" value="<?php echo $_SESSION['storecode'] ?>">
				<input type="hidden" name="staff_id" value="<?php echo $_SESSION['staff_id'] ?>">
					<?php echo $barcode; ?>,<input type="hidden" name="barcode" value="<?php echo $barcode; ?>">
					<?php echo $item_code; ?>,<input type="hidden" name="item_code" value="<?php echo $item_code; ?>">
					<?php echo $i_price; ?>,<input type="hidden" name="i_price" value="<?php echo $i_price; ?>">
					<input type="hidden" name="rack_number" value="<?php echo $_SESSION['rack_number']; ?>">
				<?php echo $i_des; ?>
				<br>
				<div class="input-group">
				<label for="inputEmail" class="control-label col-sm-2">QTY: </label>
				<div class="col-sm-5"><input type="number" class="checkqty form-control"  name="quanttity" maxlength="5" required value="" autofocus></div>
				<div class="col-sm-5"><input type="submit" class="btn btn-primary" name="save"  value="Save"/></div>
			</div><!--input-group-->
			</div><!--form-group-->
            </form>
		</div>
		<?php 
          } // Ends if($item_code == $tinto) else
        } // Ends if($i_des == '') else
		} else { ?>
		<?PHP if (isset($_GET['d']) == 1) { ?>
				<div class="alert alert-success">
					<button type="button" class="close" data-dismiss="alert"><span class="glyphicon glyphicon-minus-sign"></span></button>
				<strong>Good!</strong> Update Successful
				</div> 
		<?php } ?>

<?php } ?>   
</div>
</div><!--box-inner-->

  <!-- content ends -->
</div><!--/#content.col-md-12-->

<hr>

<?php require('footer.php'); ?>
