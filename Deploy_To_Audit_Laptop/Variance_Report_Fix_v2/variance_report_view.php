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
      $sql = oci_parse($conn, "SELECT a.VC_ITEM_CODE AS ITEM_CODE, a.ITEM_NAME, a.PRICE, a.VC_AUDIT_QTY AS QTY, a.DEPT, mi.CURR_STOCK, mi.VC_GROUP, mi.VC_SUBGROUP
 FROM ZS_STOCK_AUDIT_ERP_NEW a
 LEFT JOIN MASTER_ITEM mi ON a.VC_ITEM_CODE = mi.ITEM_CODE AND a.VC_SHOP_CODE = mi.SHOP_CODE
 WHERE a.VC_SHOP_CODE = '{$cant}'");
      oci_execute($sql);

      // Fetch Distinct Zones per Item
      $zone_query = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, RACK_NUM FROM ZS_VW_AUDIT_REPORT WHERE SHOP_CODE = '{$cant}'");
      oci_execute($zone_query);
      $item_zones = [];
      while ($zrow = oci_fetch_assoc($zone_query)) {
          $icode = $zrow['ITEM_CODE'];
          if (!isset($item_zones[$icode])) {
              $item_zones[$icode] = [];
          }
          $item_zones[$icode][] = $zrow['RACK_NUM'];
      }
      ?>
      <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Audit Number</th>
            <th>ITEM_CODE</th>
            <th>ITEM_NAME</th>
            <th>PRICE</th>
            <th>DEPT</th>
            <th>GROUPS</th>
            <th>SUBGROUPS</th>
            <th>ERP_QTY</th>
            <th>AUDIT_QTY</th>
            <th>DIFF</th>
            <th>DIFF VALUE</th>
            <th>ZONES</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          while ($trica = oci_fetch_array($sql)) {
            $ITEM_CODE = $trica['ITEM_CODE'];
            $ITEM_NAME = $trica['ITEM_NAME'];
            $PRICE = $trica['PRICE'];
            $DEPT = $trica['DEPT'];
            $GROUPS = isset($trica['VC_GROUP']) ? $trica['VC_GROUP'] : '';
            $SUBGROUPS = isset($trica['VC_SUBGROUP']) ? $trica['VC_SUBGROUP'] : '';
            $ERP_QTY = $trica['CURR_STOCK'];
            $AUDIT_QTY = $trica['QTY'];
            $DIFF = $AUDIT_QTY - $ERP_QTY;
            $DIFFVALUE = $DIFF * $PRICE;
            $ZONES = isset($item_zones[$ITEM_CODE]) ? implode(", ", $item_zones[$ITEM_CODE]) : '';
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <td class="center"><?php echo $ITEM_CODE; ?></td>
              <td class="center"><?php echo $ITEM_NAME; ?></td>
              <td class="center"><?php echo $PRICE; ?></td>
              <td class="center"><?php echo $DEPT; ?></td>
              <td class="center"><?php echo $GROUPS; ?></td>
              <td class="center"><?php echo $SUBGROUPS; ?></td>
              <td class="center"><?php echo $ERP_QTY; ?></td>
              <td class="center"><?php echo $AUDIT_QTY; ?></td>
              <td class="center"><?php echo $DIFF; ?></td>
              <td class="center"><?php echo $DIFFVALUE; ?></td>
              <td class="center"><?php echo $ZONES; ?></td>
            </tr><?php $count++;
          } 

          $sqlpending = oci_parse($conn, "SELECT p.*, mi.VC_GROUP, mi.VC_SUBGROUP FROM ZS_VW_AUDIT_PENDING p LEFT JOIN MASTER_ITEM mi ON p.ITEM_CODE = mi.ITEM_CODE AND p.SHOP_CODE = mi.SHOP_CODE WHERE p.SHOP_CODE = '{$cant}'");
          oci_execute($sqlpending);

          while ($trica = oci_fetch_array($sqlpending)) {
            $ITEM_CODE = $trica['ITEM_CODE'];
            $ITEM_NAME = $trica['ITEM_NAME'];
            $PRICE = $trica['PRICE'];
            $DEPT = $trica['DEPT'];
            $GROUPS = isset($trica['VC_GROUP']) ? $trica['VC_GROUP'] : '';
            $SUBGROUPS = isset($trica['VC_SUBGROUP']) ? $trica['VC_SUBGROUP'] : '';
            $ERP_QTY = $trica['CURR_STOCK'];
            $AUDIT_QTY = 0;
            $DIFF = $AUDIT_QTY - $ERP_QTY;
            $DIFFVALUE = $DIFF * $PRICE;
            $ZONES = '';
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <td class="center"><?php echo $ITEM_CODE; ?></td>
              <td class="center"><?php echo $ITEM_NAME; ?></td>
              <td class="center"><?php echo $PRICE; ?></td>
              <td class="center"><?php echo $DEPT; ?></td>
              <td class="center"><?php echo $GROUPS; ?></td>
              <td class="center"><?php echo $SUBGROUPS; ?></td>
              <td class="center"><?php echo $ERP_QTY; ?></td>
              <td class="center"><?php echo $AUDIT_QTY; ?></td>
              <td class="center"><?php echo $DIFF; ?></td>
              <td class="center"><?php echo $DIFFVALUE; ?></td>
              <td class="center"><?php echo $ZONES; ?></td>
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
