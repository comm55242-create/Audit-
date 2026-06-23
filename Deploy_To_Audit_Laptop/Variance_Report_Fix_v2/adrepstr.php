<?php include('header.php'); ?>
<?php confirm_logged_in(); ?>
<?php
//$gotru = $_GET['tanto'];
//if(empty($gotru)){
//redirect_to("index.php");
//}
$cant = $_SESSION['storecode'];
if (isset($_GET['not'])) {

  $sql = oci_parse($conn, "SELECT ITEM_CODE,AUDIT_QTY,shop_code FROM ZS_STOCK_AUDIT_ERP WHERE VC_SHOP_CODE = '{$cant}'");
  if (!$sql) {
    $e2 = oci_error($conn);
    trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
  }
  $r2 = oci_execute($sql);
  $dtime = date('Y-m-d H:i:s');
  $dron2 = 'C:\report\Initial_stock_count.csv';
  $fp = fopen($dron2, 'w');

  while ($row = oci_fetch_assoc($sql)) {
    fputcsv($fp, $row);
    error_reporting(0);
  }
  ?>
  <div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Export successful on!</strong> <a href="<?php echo 'C:\report\Initial_stock_count.csv'; ?>" download = "<?php echo 'Initial_stock_count' . $gotru; ?>"><?php echo 'C:\report\Initial_stock_count' . $cant . $dtime . '.csv'; ?></a>
  </div> 
  <?php
  fclose($fp);

  //close the db connection
  //mysql_close();
}
?>


<style>
    
    #ealertdiv{
      display: none;
    }
</style>
<div id="content" class="col-lg-12">
  <!-- content starts -->
  <div class="box-inner">
    <div class="box-header well">
      <h2><i class="glyphicon glyphicon-info-sign"></i> Audit Report on: <?php echo $_SESSION['storecode']; ?>.</h2>

      <div class="box-icon">
        <a href="#" class="btn btn-minimize btn-round btn-default"><i
            class="glyphicon glyphicon-chevron-up"></i></a>
        <a href="#" class="btn btn-close btn-round btn-default"><i
            class="glyphicon glyphicon-remove"></i></a>
      </div>
    </div>
	<div class="container-fluid">
	<br>
	 <ol class="breadcrumb">
		<li><a href="#">Home</a></li>
		<li><a href="#">Audit</a></li>
		<li>Consolidated Discrepancy Report</li>
	</ol>
    <div class="box-content row"> 
      <p>
        <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=2"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv
        </a> -->

        <button id='exportcdiscreport' class="btn btn-primary" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

      </p> 

      <div id='ealertdiv' class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Export successful on!</strong> 
        <a id='dfile'
          href="" 
          download = "">
            
          </a>
      </div>

      <?php
      $sql = oci_parse($conn, "SELECT SHOP_CODE, ITEM_CODE, PRICE, ITEM_NAME, SUM(QTY)QTY, AVG(CURR_STOCK) CURR_STOCK, DEPT
 FROM 
  (
    SELECT SHOP_CODE,ITEM_CODE, PRICE, ITEM_NAME, QTY, DEPT, CURR_STOCK, rack_num,user_name,SUBSTR(DATE_SYS,1,10)DATE_SYS 
    FROM ZS_VW_AUDIT_REPORT a
  ) ab
  WHERE SHOP_CODE = '{$cant}'
  GROUP BY ITEM_CODE, PRICE, ITEM_NAME, DEPT, SHOP_CODE");
      oci_execute($sql);
      ?>
      <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Audit Number</th>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Item Price</th>
            <th>Audit Qty</th>
            <th>ERP QTY</th>
            <th>DIFF</th>
            <th>DIFFVALUE</th>
            <th>DEPT</th>


          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          while ($trica = oci_fetch_array($sql)) {
            $SHOP_CODE = $trica['SHOP_CODE'];
            $ITEM_CODE = $trica['ITEM_CODE'];
            $ITEM_NAME = $trica['ITEM_NAME'];
            $AUDIT_QTY = $trica['QTY'];
            $ERP_QTY = $trica['CURR_STOCK'];
            $DEPT = $trica['DEPT'];
            $PRICE = $trica['PRICE'];
            $DIFF = $AUDIT_QTY - $ERP_QTY;
            $DIFFVALUE = $DIFF * $PRICE;
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <!-- <td class="center"><?php echo $SHOP_CODE; ?></td> -->
              <td class="center"><?php echo $ITEM_CODE; ?></td>
              <td class="center"><?php echo $ITEM_NAME; ?></td>
              <td class="center"><?php echo $PRICE; ?></td>
              <td class="center"><?php echo $AUDIT_QTY; ?></td>
              <td class="center"><?php echo $ERP_QTY; ?></td>
              <td class="center"><?php echo $DIFF; ?></td>
              <td class="center"><?php echo $DIFFVALUE; ?></td>
              <td class="center"><?php echo $DEPT; ?></td>

            </tr><?php $count++;
          } 

          $sqlpending = oci_parse($conn, "SELECT * FROM ZS_VW_AUDIT_PENDING");
          oci_execute($sqlpending);

          while ($trica = oci_fetch_array($sqlpending)) {
            $SHOP_CODE = $trica['SHOP_CODE'];
            $ITEM_CODE = $trica['ITEM_CODE'];
            $ITEM_NAME = $trica['ITEM_NAME'];
            $AUDIT_QTY = 0;
            $ERP_QTY = $trica['CURR_STOCK'];
            $DEPT = $trica['DEPT'];
            $PRICE = $trica['PRICE'];
            $DIFF = $AUDIT_QTY - $ERP_QTY;
            $DIFFVALUE = $DIFF * $PRICE;
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <!-- <td class="center"><?php echo $SHOP_CODE; ?></td> -->
              <td class="center"><?php echo $ITEM_CODE; ?></td>
              <td class="center"><?php echo $ITEM_NAME; ?></td>
              <td class="center"><?php echo $PRICE; ?></td>
              <td class="center"><?php echo $AUDIT_QTY; ?></td>
              <td class="center"><?php echo $ERP_QTY; ?></td>
              <td class="center"><?php echo $DIFF; ?></td>
              <td class="center"><?php echo $DIFFVALUE; ?></td>
              <td class="center"><?php echo $DEPT; ?></td>

            </tr><?php $count++;
          } ?>
        </tbody>
      </table>

    </div></div></div>

  <!-- content ends -->
</div><!--/#content.col-md-0-->
</div><!--/fluid-row-->

<!-- Ad, you can remove it --> 

</div>
<!-- Ad ends -->

<hr>

<?php require('footer.php'); ?>
<script type="text/javascript">


  $('#exportcdiscreport').click(function(){

    $.ajax({
      url:'postfolder/export.php',
      type:'post',
      data:'cdiscreport=ok',
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
  

</script>
