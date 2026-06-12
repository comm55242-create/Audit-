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

        // Search by either BARCODE or ITEM_CODE and handle trailing spaces
        $result = oci_parse($conn, "SELECT DISTINCT BARCODE, ITEM_CODE, ITEM_NAME, PRICE, VC_UNIT FROM MASTER_ITEM WHERE TRIM(BARCODE) = UPPER(TRIM('{$txtbarcode}')) OR TRIM(ITEM_CODE) = UPPER(TRIM('{$txtbarcode}'))");
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
        $vc_unit = isset($toto['VC_UNIT']) ? trim($toto['VC_UNIT']) : '';
        $step_attr = '';
        if (strtoupper($vc_unit) == 'KGS' || strtoupper($vc_unit) == 'KSG') {
            $step_attr = 'step="any"';
        }

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
            <strong>Error!</strong> No Record Found for "<?php echo htmlspecialchars($txtbarcode); ?>"
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
		<div class="container-fluid" style="margin-top: 15px;">
            <div class="panel panel-info" style="border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 1px solid #bce8f1;">
                <div class="panel-heading" style="border-top-left-radius: 7px; border-top-right-radius: 7px; background-color: #d9edf7; padding: 12px 15px;">
                    <h3 class="panel-title" style="margin: 0; font-weight: bold; color: #31708f; font-size: 16px;"><i class="glyphicon glyphicon-tag"></i> Item Details</h3>
                </div>
                <div class="panel-body" style="padding: 20px;">
                    <div class="row">
                        <div class="col-md-7">
                            <h4 style="color: #2c3e50; font-weight: 800; font-size: 18px; margin-top: 0; margin-bottom: 15px;"><?php echo $i_des; ?></h4>
                            <p style="margin-bottom: 8px;"><strong style="color: #7f8c8d;">Barcode:</strong> <span style="font-family: monospace; font-size: 14px; background: #ecf0f1; padding: 2px 6px; border-radius: 4px;"><?php echo $barcode; ?></span></p>
                            <p style="margin-bottom: 8px;"><strong style="color: #7f8c8d;">Item Code:</strong> <span style="font-family: monospace; font-size: 14px; background: #ecf0f1; padding: 2px 6px; border-radius: 4px;"><?php echo $item_code; ?></span></p>
                            <p style="margin-bottom: 0;"><strong style="color: #7f8c8d;">Unit:</strong> <span class="label label-primary" style="font-size: 12px; padding: 4px 8px;"><?php echo $vc_unit ?: 'PCS'; ?></span></p>
                        </div>
                        <div class="col-md-5">
                            <form action="update.php?un=<?php echo $_SESSION['username']; ?>&rn=<?php echo $_SESSION['rack_number'];?>" method="post" class="form-horizontal">
                                <input type="hidden" name="s_code" value="<?php echo $_SESSION['storecode'] ?>">
                                <input type="hidden" name="staff_id" value="<?php echo $_SESSION['staff_id'] ?>">
                                <input type="hidden" name="barcode" value="<?php echo $barcode; ?>">
                                <input type="hidden" name="item_code" value="<?php echo $item_code; ?>">
                                <input type="hidden" name="i_price" value="<?php echo $i_price; ?>">
                                <input type="hidden" name="rack_number" value="<?php echo $_SESSION['rack_number']; ?>">
                                
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="control-label" style="text-align: left; margin-bottom: 8px; display: block; color: #34495e;">Enter Quantity:</label>
                                    <div class="input-group">
                                        <input type="number" <?php echo $step_attr; ?> class="form-control" style="border-width: 2px; border-color: #bdc3c7; font-size: 18px; font-weight: bold; height: 46px; text-align: center;" name="quanttity" maxlength="10" required placeholder="<?php echo $step_attr ? 'e.g. 5.5' : 'e.g. 10'; ?>" autofocus>
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-success" name="save" style="height: 46px; padding: 0 20px; font-weight: bold; font-size: 15px; border-width: 2px;"><i class="glyphicon glyphicon-floppy-disk"></i> Save</button>
                                        </span>
                                    </div>
                                    <?php if($step_attr) { ?>
                                        <small style="color: #e67e22; display: block; margin-top: 8px; font-weight: bold;">* Decimals are allowed for KGS items</small>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
