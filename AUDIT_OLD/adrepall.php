<?php include('header.php'); ?>
<?php confirm_logged_in(); ?>
<?php
$sc = $_SESSION['storecode'];
?>

<style>
    #ealertdiv{
      display: none;
    }
    .exportbutton{
      margin-bottom:30px;
    }

    #pendingform{
      margin: 20px 0px;
      background-color: #8080801c;
      padding:20px;
      display: none;
    }
    #viewanother{
      display: inline-block;
      margin-left: 50px;
      font-size: 12px;
      cursor: pointer;
      text-decoration: underline;
    }
    #viewanother:active{
      transform: scale(1.05);
    }
</style>
<div id="content" class="col-lg-12">
  <!-- content starts -->
  <div class="box-inner">
    <div class="box-header well">
      <h2><i class="glyphicon glyphicon-info-sign"></i> Audit Report on : <?php echo $_SESSION['storecode']; ?>. Audited by:  All.</h2>

      <div class="box-icon">
        <a href="#" class="btn btn-minimize btn-round btn-default">
          <i class="glyphicon glyphicon-chevron-up"></i>
        </a>
        <a href="#" class="btn btn-close btn-round btn-default">
          <i class="glyphicon glyphicon-remove"></i>
        </a>
      </div>
    </div>
	<div class="container-fluid">
    <?php // $origin = $_GET['addon']; ?>

    <?php if (isset($_GET['addon']) && $_GET['addon'] == "yes"): ?>
	<br>
	<ol class="breadcrumb">
		<li><a href="#">Home</a></li>
		<li><a href="#">Audit</a></li>
		<li>Round 1<sup>st</sup> Report</li>
	</ol>
      <div class="box-content row"> <p>


        <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=3">
	       <i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv
        </a> -->

        <button id='auditfirstrount' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

        <div id='ealertdiv' class="alert alert-success">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>Export successful on!</strong> 
          <a id='dfile'
              href="" 
              download = ""> 
          </a>
        </div>

	  <!-- <button id='upload' class='btn btn-info'> Upload</button>

    <span id='itemcount' style="display:inline-block;margin-left: 20px;font-weight: bold; font-size: 20px;color: black;"></span>
    
    <div id='loadingDiv'>
      <div id='progress' >
        
      </div>
      <span id='loadingText'><span id='loadingSpan'>0</span>%</span>
    </div> -->
	  </p>     
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
    
    <?php if(isset($_GET['addon']) && $_GET['addon']=="default"): ?>
	<br>
	<ol class="breadcrumb">
		<li><a href="#">Home</a></li>
		<li><a href="#">Audit</a></li>
		<li>View Reports</li>
	</ol>
    <div class="box-content row"> <p>
		<button id='viewreportse' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>
		<div id='ealertdiv' class="alert alert-success">
		  <button type="button" class="close" data-dismiss="alert">×</button>
		  <strong>Export successful on!</strong> 
		  <a id='dfile' href="" download=""></a>
		</div>
	</p>     
        <?php
        $sql = oci_parse($conn, "SELECT ITEM_CODE, QTY, DATE_SYS FROM HEAD_AUDIT ORDER BY DATE_SYS DESC");
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
	
	<!-- START -->
<?php if (isset($_GET['addon']) && $_GET['addon'] == "audit2"): ?>
	<br>
	<ol class="breadcrumb">
		<li><a href="#">Home</a></li>
		<li><a href="#">Audit</a></li>
		<li>Audit 2<sup>nd</sup> Report</li>
	</ol>
      <div class="box-content row"> <p><a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=4">
	  <i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</a></p>     
      <?php
      $sql = oci_parse($conn, "SELECT ITEM_CODE, PRICE, ITEM_NAME, RACK_NUM, USER_NAME, QTY, DATE_SYS FROM ZS_VW_AUDIT_REPORT_round WHERE SHOP_CODE = '{$sc}'");
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
	
	<!---END-->
	
	
    
    <?php if (isset($_POST['pending_stock']) && $_POST['pending_department']=="All"): ?>
	<br>
    <ol class="breadcrumb">
  		<li><a href="#">Home</a></li>
  		<li><a href="#">Audit</a></li>
  		<li>Pending Stock Report</li>

      <span id='viewanother'>View another one</span>
  	</ol>

    <?php
      $sqlpending = oci_parse($conn, "SELECT DISTINCT DEPT, COUNT(ITEM_CODE) co  FROM ZS_VW_AUDIT_PENDING GROUP BY DEPT");
      if (!$sqlpending) {
      $e2 = oci_error($conn);
      trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
      }
      $rsqlpending = oci_execute($sqlpending);
    ?>

    <form id='pendingform' method="POST" action="adrepall.php">
      <div class="form-group">
        <label for="inputSelectDepartment">Select Department</label>
        <select name="pending_department" class="form-control">
        <option>All</option>                    
        <?php while ($row = oci_fetch_array($sqlpending)): ?>
        <option value="<?php echo $row['DEPT']; ?>"><?php echo $row['DEPT']; ?> (<?php echo $row['CO']; ?>)</option>
        <?php endwhile; ?>
        </select>
      </div><!--form-group-->
      <input name="pending_stock" type="submit" value="View Pending Stock Take" class="btn btn-info">
    </form>


    <div class="box-content row"> <p>
      <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=2"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</a> -->

      <button id='pendingalle' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

      <div id='ealertdiv' class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Export successful on!</strong> 
        <a id='dfile'
            href="" 
            download = ""> 
        </a>
      </div>


    </p>     
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
              <th>Value</th>

            </tr>
          </thead>
          <tbody>
            <?php
            $count = 1;
            while ($trica = oci_fetch_array($sql)) {
              $item_code = $trica['ITEM_CODE'];
              $item_name = $trica['ITEM_NAME'];
              $item_dept = $trica['DEPT'];
              $price = $trica['PRICE'];
              $qty = $trica['CURR_STOCK'];
              ?>
              <tr>
                <td><?php echo $count; ?></td>
                <td class="center"><?php echo $item_code; ?></td>
                <td class="center"><?php echo $item_name; ?></td>
                <td class="center">
                  <?php echo $item_dept; ?>
                </td>
                <td class="center"><?= $qty * $price; ?> </td>
              </tr><?php
              $count++;
            }
            ?>
          </tbody>
        </table>

      </div>

    <?php endif; ?>
    
    
    <?php if (isset($_POST['pending_stock']) && $_POST['pending_department'] !="All"): ?>
    <?php $chosen_dept = $_POST['pending_department'] ?>

        

        <ol class="breadcrumb">
          <li><a href="#">Home</a></li>
          <li><a href="#">Audit</a></li>
          <li><a href="#">Pending Stock Report</a></li>
          <li id='cat' data-cat="<?= urlencode($chosen_dept) ?>"><?= $chosen_dept ?></li>

          <span id='viewanother'>View another one</span>
        </ol>

        <?php
          $sqlpending = oci_parse($conn, "SELECT DISTINCT DEPT, COUNT(ITEM_CODE) co FROM ZS_VW_AUDIT_PENDING GROUP BY DEPT");
          if (!$sqlpending) {
          $e2 = oci_error($conn);
          trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
          }
          $rsqlpending = oci_execute($sqlpending);
        ?>

        <form id='pendingform' method="POST" action="adrepall.php">
          <div class="form-group">
            <label for="inputSelectDepartment">Select Department</label>
            <select name="pending_department" class="form-control">
            <option>All</option>                    
            <?php while ($row = oci_fetch_array($sqlpending)): ?>
            <option value="<?php echo $row['DEPT']; ?>" <?= ($chosen_dept== $row['DEPT']) ? 'selected' : '' ?>><?php echo $row['DEPT']; ?> (<?php echo $row['CO']; ?>)</option>
            <?php endwhile; ?>
            </select>
          </div><!--form-group-->
          <input name="pending_stock" type="submit" value="View Pending Stock Take" class="btn btn-info">
        </form>


        <button id='pendingdepte' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

        <div id='ealertdiv' class="alert alert-success">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>Export successful on!</strong> 
          <a id='dfile'
              href="" 
              download = ""> 
          </a>
        </div>

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
              <th>Value</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $count = 1;
            while ($trica = oci_fetch_array($sql)) {
              $item_code = $trica['ITEM_CODE'];
              $item_name = $trica['ITEM_NAME'];
              $item_dept = $trica['DEPT'];
              $price = $trica['PRICE'];
              $qty = $trica['CURR_STOCK'];
              ?>
              <tr>
                <td><?php echo $count; ?></td>
                <td class="center"><?php echo $item_code; ?></td>
                <td class="center"><?php echo $item_name; ?></td>
                <td class="center">
                  <?php echo $item_dept; ?>
                </td>
                <td class="center"><?= $qty * $price; ?> </td>
              </tr><?php
              $count++;
            }
            ?>
          </tbody>
        </table>

      </div>

    <?php endif; ?>
  
  <!--discrepancy-->
  <?php if (isset($_GET['addon']) && $_GET['addon'] == "discrepancy"): ?>
	 <br>
	 <ol class="breadcrumb">
		<li><a href="#">Home</a></li>
		<li><a href="#">Audit</a></li>
		<li>Provisional Discrepancy Report</li>
	</ol>
<div class="box-content row"> 

  <p>

    <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=2"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</a> -->

    <button id='provdiscreporte' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

    <div id='ealertdiv' class="alert alert-success">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Export successful on!</strong> 
      <a id='dfile'
          href="" 
          download = ""> 
      </a>
    </div>

  </p>     
        <?php



        // SELECT ITEM_CODE, PRICE, ITEM_NAME, USER_NAME, SUM(QTY)QTY, AVG(CURR_STOCK) CURR_STOCK,rack_num,DATE_SYS 
        //          FROM 
        //           (
        //             SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
        //             FROM ZS_VW_AUDIT_REPORT a
        //           ) 
        //           WHERE SHOP_CODE = '{$sc}' 
        //           GROUP BY ITEM_CODE, PRICE, ITEM_NAME,USER_NAME,rack_num,DATE_SYS

        $sql = oci_parse($conn, "SELECT ITEM_CODE, PRICE, ITEM_NAME, USER_NAME, SUM(QTY)QTY, AVG(CURR_STOCK) CURR_STOCK,rack_num,DATE_SYS
 FROM 
  (
    SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
    FROM ZS_VW_AUDIT_REPORT a
  ) ab
  WHERE SHOP_CODE = '{$sc}' 
  GROUP BY ITEM_CODE, PRICE, ITEM_NAME,USER_NAME,rack_num,DATE_SYS");
        if (!$sql) {
          $e2 = oci_error($conn);
          trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
        }
        $r2 = oci_execute($sql);
        ?>
      <?php
      $sql = oci_parse($conn, "SELECT ITEM_CODE, PRICE, ITEM_NAME, USER_NAME, SUM(QTY)QTY, AVG(CURR_STOCK) CURR_STOCK,rack_num,DATE_SYS
 FROM 
  (
    SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
    FROM ZS_VW_AUDIT_REPORT a
  ) ab
  WHERE SHOP_CODE = '{$sc}' 
  GROUP BY ITEM_CODE, PRICE, ITEM_NAME,USER_NAME,rack_num,DATE_SYS"
					  );
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
            <!-- <th>Total Audit Qty</th> -->
            <th>DIFF</th>
            <th>DIFF. Value</th>
            <!-- <th>(%)TAGE</th> -->
            <th>Date/Time</th>
            <th>User</th>
            <th>Zone</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          while ($trica = oci_fetch_array($sql)) {

            // print_r($trica);
            $tinto = $trica['ITEM_CODE'];
            $item_name = $trica['ITEM_NAME'];
            $item_price = $trica['PRICE'];
            $qty = $trica['QTY'];
            
            $user = $trica['USER_NAME'];
            $rack = $trica['RACK_NUM'];


            // $ttCountQty = $trica['TOTAL_COUNT_QTY'];
            $shop_qty = $trica['CURR_STOCK'] != 0 ? $trica['CURR_STOCK'] : 1;
            $diff = intval($qty) - intval($shop_qty);
            $diff_val = $item_price * $diff;
            // $perc = abs(($diff/intval($shop_qty))*100);
            $Date = $trica['DATE_SYS'];
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <td class="center"><?php echo $tinto; ?></td>
              <td class="center"><?php echo $item_name; ?></td>
              <td class="center"><?php echo $item_price; ?></td>
              <td class="center"><?php echo $qty; ?></td>
              <td class="center"><?php echo $shop_qty; ?></td>
              <!-- <td class="center"><?php echo $ttCountQty; ?></td> -->
              <td class="center"><?php echo $diff; ?></td>
              <td class="center"><?php echo $diff_val; ?></td>
              <!-- <td class="center"><?php echo $perc; ?></td> -->
              <td class="center">
                <?php echo $Date; ?>
              </td>
              <td class="center"><?= $user; ?></td>
              <td class="center"><?= $rack; ?></td>

            </tr><?php $count++;
          }
              ?>
        </tbody>
      </table>

    </div>
  <?php endif; ?>
    
<!-- audit report 2"-->


<!-- -->


<!-- =========================
     =========================
     ==== EXPORT BUTTONS =====
     =========================
     =========================
-->

<?php 
  if (isset($_GET['not']) && $_GET['not'] == 2) {

  $sql = oci_parse($conn, "SELECT item_code,shop_code,AUDIT_QTY QTY FROM ZS_STOCK_AUDIT_ERP WHERE SHOP_CODE = '{$sc}' ");
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
  $dron2 = 'C:\report\Round1' . $dtime . '.csv';
  $fp = fopen($dron2, 'w');

  while ($row = oci_fetch_assoc($sql)) {
    fputcsv($fp, $row);
    error_reporting(0);
  }
  ?>
    <div class="alert alert-success">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Export Successful on!</strong> <a href="<?php echo 'C:\report\Round1' . $dtime . '.csv'; ?> download = "<?php echo 'booksll'; ?>"><?php echo 'C:\report\booksall' . $dtime . '.csv'; ?></a>
    </div> 
  <?php
  fclose($fp);

  //close the db connection
  //mysql_close();
}
//-------------------------------------
if (isset($_GET['not']) && $_GET['not'] == 4) {

  $sql = oci_parse($conn, "SELECT ITEM_CODE, PRICE, ITEM_NAME, SHOP_CODE, QTY, DATE_SYS FROM ZS_VW_AUDIT_REPORT_round WHERE SHOP_CODE = '{$sc}'");
  oci_execute($sql);
  $dtime = date('Y-m-d H,i,s');
  $dron2 = 'C:\report\Round2' . $dtime . '.csv';
  $fp = fopen($dron2, 'w');

  while ($row = oci_fetch_assoc($sql)) {
    fputcsv($fp, $row);
    error_reporting(0);
  }
  ?>
    <div class="alert alert-success">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Export Successful on!</strong> <a href="<?php echo 'C:\report\Round2' . $dtime . '.csv'; ?> download = "<?php echo 'booksll'; ?>><?php echo 'C:\report\booksall' . $dtime . '.csv'; ?></a>
    </div> 
  <?php
  fclose($fp);

  //close the db connection
  //mysql_close();
}
//-------------------------------------

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
<script type="text/javascript">
  
  $('#auditfirstrount').click(function(){

    $.ajax({
      url:'postfolder/export.php',
      type:'post',
      data:'auditfirstrount=ok',
      dataType:'text',
      success: function(donne, status){
        data = donne.trim()

        $('#dfile')
        .attr('href', 'export/'+data)
        .attr('download',data)
        .text(data)
        // console.log(donne)
        $('#ealertdiv').slideDown(300)
      },
      error:function(){

      }

    })
  })
  $('#provdiscreporte').click(function(){

    $.ajax({
      url:'postfolder/export.php',
      type:'post',
      data:'provdiscreporte=ok',
      dataType:'text',
      success: function(donne, status){
        data = donne.trim()

        $('#dfile')
        .attr('href', 'export/'+data)
        .attr('download',data)
        .text(data)
        // console.log(donne)
        $('#ealertdiv').slideDown(300)
      },
      error:function(){

      }

    })
  })
  $('#pendingalle').click(function(){

    $.ajax({
      url:'postfolder/export.php',
      type:'post',
      data:'pendingalle=ok',
      dataType:'text',
      success: function(donne, status){
        data = donne.trim()

        $('#dfile')
        .attr('href', 'export/'+data)
        .attr('download',data)
        .text(data)
        // console.log(donne)
        $('#ealertdiv').slideDown(300)
      },
      error:function(){

      }

    })
  })

  $('#pendingdepte').click(function(){

    var category = $('#cat').attr('data-cat');


    $.ajax({
      url:'postfolder/export.php',
      type:'post',
      data:'pendingdepte=ok&cat='+category,
      dataType:'text',
      success: function(donne, status){
        data = donne.trim()

        $('#dfile')
        .attr('href', 'export/'+data)
        .attr('download',data)
        .text(data)
        $('#ealertdiv').slideDown(300)
      },
      error:function(){

      }

    })
  })

  $('#viewanother').click(function(){
    $('#pendingform').slideToggle(50)
  })

  $('#viewreportse').click(function(){
    $.ajax({
      url:'postfolder/export.php',
      type:'post',
      data:'viewreportse=ok',
      dataType:'text',
      success: function(donne, status){
        data = donne.trim()
        $('#dfile')
        .attr('href', 'export/'+data)
        .attr('download',data)
        .text(data)
        $('#ealertdiv').slideDown(300)
      },
      error:function(){
      }
    })
  })

</script>