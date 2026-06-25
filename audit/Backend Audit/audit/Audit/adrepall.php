<?php include('header.php'); ?>
<?php confirm_logged_in(); ?>
<?php
$sc = $_SESSION['storecode'];
?>
<div id="content" class="col-lg-12">
  <!-- content starts -->
  <div class="box-inner">
    <div class="box-header well">
      <h2><i class="glyphicon glyphicon-info-sign"></i> Audit Report on: <?php echo $_SESSION['storecode']; ?>. Audited by:  All.</h2>

      <div class="box-icon">
        <a href="#" class="btn btn-minimize btn-round btn-default">
          <i class="glyphicon glyphicon-chevron-up"></i>
        </a>
        <a href="#" class="btn btn-close btn-round btn-default">
          <i class="glyphicon glyphicon-remove"></i>
        </a>
      </div>
    </div>
    <?php // $origin = $_GET['addon']; ?>

    <?php if (isset($_GET['addon']) && $_GET['addon'] == "yes"): ?>

      <div class="box-content row"> 
	  <p><a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=3"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</a></p>     
      <?php
      $sql = oci_parse($conn, "SELECT ITEM_CODE, PRICE, ITEM_NAME, RACK_NUM, USER_NAME, QTY, DATE_SYS FROM ZS_VW_AUDIT_REPORT WHERE SHOP_CODE = '{$sc}'");
      if (!$sql) {
        $e2 = oci_error($conn);
        trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
      }
      $r2 = oci_execute($sql);
      ?>
      <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Audit Number</th>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Item Price</th>
            <th>Qty</th>
            <th>User</th>
            <th>Rack</th>
            <th>Date/Time</th>

          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          while ($trica = oci_fetch_array($sql)) {
            $tinto = $trica['ITEM_CODE'];
            $item_name = $trica['ITEM_NAME'];
            $item_price = $trica['PRICE'];
            $Qty = $trica['QTY'];
            $user = $trica['USER_NAME'];
            $rack = $trica['RACK_NUM'];
            $Date = $trica['DATE_SYS'];
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <td class="center"><?php echo $tinto; ?></td>
              <td class="center"><?php echo $item_name; ?></td>
              <td class="center"><?php echo $item_price; ?></td>
              <td class="center"><?php echo $Qty; ?></td>
              <td class="center"><?php echo $user; ?></td>
              <td class="center"><?php echo $rack; ?></td>
              <td class="center">
                <?php echo $Date; ?>
              </td>

            </tr><?php $count++;
            } ?>
        </tbody>
      </table>

    </div>
    <?php endif; ?>
	
	<!--------START  --!>
	
 <?php if (isset($_POST['audit2']) && $_POST['audit2']=="All"): ?>

    <div class="box-content row"> <p><a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=2"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</a></p>     
        <?php
        $sql = oci_parse($conn, "SELECT * FROM ZS_VW_AUDIT_PENDING");
        if (!$sql) {
          $e2 = oci_error($conn);
          trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $r2 = oci_execute($sql);
        ?>
        <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>#</th>
              <th>Item Code</th>
              <th>Item Name</th>
              <th>Department</th>

            </tr>
          </thead>
          <tbody>
            <?php
            $count = 1;
            while ($trica = oci_fetch_array($sql)) {
              $item_code = $trica['ITEM_CODE'];
              $item_name = $trica['ITEM_NAME'];
              $item_dept = $trica['DEPT'];
              ?>
              <tr>
                <td><?php echo $count; ?></td>
                <td class="center"><?php echo $item_code; ?></td>
                <td class="center"><?php echo $item_name; ?></td>
                <td class="center">
                  <?php echo $item_dept; ?>
                </td>

              </tr><?php
              $count++;
            }
            ?>
          </tbody>
        </table>

      </div>

    <?php endif; ?>
	
	<!---END-->
	
    
    <?php if(isset($_GET['addon']) && $_GET['addon']=="default"): ?>
    
    <div class="box-content row"> <p><a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=2">
	<i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</a></p>     
        <?php
        $sql = oci_parse($conn, "SELECT ITEM_CODE, QTY, DATE_SYS FROM HEAD_AUDIT WHERE SHOP_CODE = '{$sc}'");
        if (!$sql) {
          $e2 = oci_error($conn);
          trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $r2 = oci_execute($sql);
        ?>
        <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>Audit Number</th>
              <th>Item Code</th>
              <th>Qty</th>
              <th>Date/Time</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $count = 1;
            while ($trica = oci_fetch_array($sql)) {
              $tinto = $trica['ITEM_CODE'];
              $Qty = $trica['QTY'];
              $Date = $trica['DATE_SYS'];
              ?>
              <tr>
                <td><?php echo $count; ?></td>
                <td class="center"><?php echo $tinto; ?></td>
                <td class="center"><?php echo $Qty; ?></td>
                <td class="center">
                  <?php echo $Date; ?>
                </td>

              </tr><?php $count++;
            } ?>
          </tbody>
        </table>

      </div>
      
    <?php endif; ?>
    
    <?php if (isset($_POST['pending_stock']) && $_POST['pending_department']=="All"): ?>

    <div class="box-content row"> <p><a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=2"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</a></p>     
        <?php
        $sql = oci_parse($conn, "SELECT * FROM ZS_VW_AUDIT_PENDING");
        if (!$sql) {
          $e2 = oci_error($conn);
          trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $r2 = oci_execute($sql);
        ?>
        <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>#</th>
              <th>Item Code</th>
              <th>Item Name</th>
              <th>Department</th>

            </tr>
          </thead>
          <tbody>
            <?php
            $count = 1;
            while ($trica = oci_fetch_array($sql)) {
              $item_code = $trica['ITEM_CODE'];
              $item_name = $trica['ITEM_NAME'];
              $item_dept = $trica['DEPT'];
              ?>
              <tr>
                <td><?php echo $count; ?></td>
                <td class="center"><?php echo $item_code; ?></td>
                <td class="center"><?php echo $item_name; ?></td>
                <td class="center">
                  <?php echo $item_dept; ?>
                </td>

              </tr><?php
              $count++;
            }
            ?>
          </tbody>
        </table>

      </div>

    <?php endif; ?>
	
	
	
	<!-------------------starts    ---!>
	
    
    <?php if (isset($_POST['pending_stock']) && $_POST['pending_department'] !="All"): ?>
    
    <?php $chosen_dept = $_POST['pending_department'] ?>

        <?php
        $sql = oci_parse($conn, "SELECT * FROM ZS_VW_AUDIT_PENDING WHERE DEPT = '{$chosen_dept}'");
        if (!$sql) {
          $e2 = oci_error($conn);
          trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $r2 = oci_execute($sql);
        ?>
        <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
          <thead>
            <tr>
              <th>#</th>
              <th>Item Code</th>
              <th>Item Name</th>
              <th>Department</th>

            </tr>
          </thead>
          <tbody>
            <?php
            $count = 1;
            while ($trica = oci_fetch_array($sql)) {
              $item_code = $trica['ITEM_CODE'];
              $item_name = $trica['ITEM_NAME'];
              $item_dept = $trica['DEPT'];
              ?>
              <tr>
                <td><?php echo $count; ?></td>
                <td class="center"><?php echo $item_code; ?></td>
                <td class="center"><?php echo $item_name; ?></td>
                <td class="center">
                  <?php echo $item_dept; ?>
                </td>

              </tr><?php
              $count++;
            }
            ?>
          </tbody>
        </table>

      </div>

    <?php endif; ?>
  
  
  <?php if (isset($_GET['addon']) && $_GET['addon'] == "discrepancy"): ?>

      <?php
      $sql = oci_parse($conn, "select ITEM_CODE, PRICE, ITEM_NAME, sum(QTY)QTY, avg(CURR_STOCK) CURR_STOCK,rack_num,DATE_SYS from (SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS FROM ZS_VW_AUDIT_REPORT a) WHERE SHOP_CODE = '{$sc}' group by ITEM_CODE, PRICE, ITEM_NAME,rack_num,DATE_SYS");
      if (!$sql) {
        $e2 = oci_error($conn);
        trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
      }
      $r2 = oci_execute($sql);
      ?>
      <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Audit Number</th>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Item Price</th>
            <th>Audit Qty</th>
            <th>Shop Qty</th>
            <th>DIFF</th>
            <th>DIFF. Value</th>
            <th>(%)TAGE</th>
            <th>Date/Time</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          while ($trica = oci_fetch_array($sql)) {
            $tinto = $trica['ITEM_CODE'];
            $item_name = $trica['ITEM_NAME'];
            $item_price = $trica['PRICE'];
            $qty = $trica['QTY'];
            $shop_qty = $trica['CURR_STOCK'] != 0 ? $trica['CURR_STOCK'] : 1;
            $diff = intval($qty) - intval($shop_qty);
            $diff_val = $item_price * $diff;
            $perc = abs(($diff/intval($shop_qty))*100);
            $Date = $trica['DATE_SYS'];
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <td class="center"><?php echo $tinto; ?></td>
              <td class="center"><?php echo $item_name; ?></td>
              <td class="center"><?php echo $item_price; ?></td>
              <td class="center"><?php echo $qty; ?></td>
              <td class="center"><?php echo $shop_qty; ?></td>
              <td class="center"><?php echo $diff; ?></td>
              <td class="center"><?php echo $diff_val; ?></td>
              <td class="center"><?php echo $perc; ?></td>
              <td class="center">
                <?php echo $Date; ?>
              </td>

            </tr><?php $count++;
          }
              ?>
        </tbody>
      </table>

    </div>
  <?php endif; ?>
    
    



<!-- =========================
     =========================
     ==== EXPORT BUTTONS =====
     =========================
     =========================
-->

<?php 
  if (isset($_GET['not']) && $_GET['not'] == 2) {

  $sql = oci_parse($conn, "SELECT ITEM_CODE, SHOP_CODE, SUM(QTY) QTY  FROM ZS_VW_AUDIT_REPORT WHERE SHOP_CODE = '{$sc}' GROUP BY ITEM_CODE, SHOP_CODE");
  oci_execute($sql);
  $dtime = date('Y-m-d H,i,s');
  $dron2 = 'C:\report\booksall' . $dtime . '.csv';
  $fp = fopen($dron2, 'w');

  while ($row = oci_fetch_assoc($sql)) {
    fputcsv($fp, $row);
    error_reporting(0);
  }
  ?>
    <div class="alert alert-success">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Export Successful on!</strong> <a href="<?php echo 'C:\report\booksall' . $dtime . '.csv'; ?> download = "<?php echo 'booksll'; ?>"><?php echo 'C:\report\booksall' . $dtime . '.csv'; ?></a>
    </div> 
  <?php
  fclose($fp);

  //close the db connection
  //mysql_close();
}

if (isset($_GET['not']) && $_GET['not'] == 3) {

//  $sql = oci_parse($conn, "SELECT ITEM_CODE, SHOP_CODE, SUM(QTY) QTY  FROM HEAD_AUDIT WHERE SHOP_CODE = '{$sc}' GROUP BY ITEM_CODE, SHOP_CODE");
  $sql = oci_parse($conn, "SELECT ITEM_CODE, PRICE, ITEM_NAME, SHOP_CODE, QTY, DATE_SYS FROM ZS_VW_AUDIT_REPORT WHERE SHOP_CODE = '{$sc}'");
  oci_execute($sql);
  $dtime = date('Y-m-d H,i,s');
  $dron2 = 'C:\report\booksall' . $dtime . '.csv';
  $fp = fopen($dron2, 'w');

  while ($row = oci_fetch_assoc($sql)) {
    fputcsv($fp, $row);
    error_reporting(0);
  }
  ?>
    <div class="alert alert-success">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Export Successful on!</strong> <a href="<?php echo 'C:\report\booksall' . $dtime . '.csv'; ?> download = "<?php echo 'booksll'; ?>"><?php echo 'C:\report\booksall' . $dtime . '.csv'; ?></a>
    </div> 
  <?php
  fclose($fp);

  //close the db connection
  //mysql_close();
}
?>    
    







    
    
    
  </div>

  <!-- content ends -->
</div><!--/#content.col-md-0-->
</div><!--/fluid-row-->

<!-- Ad, you can remove it --> 

</div>
<!-- Ad ends -->

<hr>
<?php require('footer.php'); ?>