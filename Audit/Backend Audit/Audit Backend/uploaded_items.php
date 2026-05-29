<?php include('header.php'); ?>
<?php confirm_logged_in(); ?>

<style>
    #loadingDiv{
      width: 80%;
      height: 30px;
      border:1px solid #54b4eb;
      border-radius: 5px;
      margin-top:20px;
      text-align: center;
      position: relative;
      margin:30px auto;
      display: none;
    }
    #loadingText{
      z-index: 100;
      position: relative;
      color:black;
      font-weight: bold;
      font-size: 20px;
    }
    #progress{
      position: absolute;
      text-align:center;
      background: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5); 
      height: 100%;
    }
    #ealertdiv{
      display: none;
    }
</style>


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
		<li>Uploaded items Report</li>
	</ol>
    <div class="box-content row"> 
      <p>
        

        <button id='uploadedreporte' class="btn btn-primary" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

        
    
      </p> 
      <div id='ealertdiv' class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Export successful on!</strong> 
        <a id='dfile'
          href="" 
          download = "">
            
          </a>
      </div>

      <div id='loadingDiv'>
        <div id='progress' >
          
        </div>
        <span id='loadingText'><span id='loadingSpan'>0</span>%</span>
      </div>    
      <?php
      $sql = oci_parse($conn, "SELECT VC_ITEM_CODE,VC_SHOP_CODE,NU_QTY FROM  POS.TEMP_DT_STOCK_ADJUSTMENT@db_link_shop WHERE vc_shop_code = '{$cant}'");
      oci_execute($sql);
      ?>
      <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>No.</th>
            <th>ITEM CODE</th>
            <th>SHOP CODE</th>
            <th>QTY</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          while ($trica = oci_fetch_array($sql)) {
            
            $VC_SHOP_CODE = $trica['VC_SHOP_CODE'];
            $ITEM_CODE = $trica['VC_ITEM_CODE'];
            $QTY = $trica['NU_QTY'];
            
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <td class="center"><?php echo $ITEM_CODE; ?></td>
              <td class="center"><?php echo $VC_SHOP_CODE; ?></td> 
              <td class="center"><?php echo $QTY; ?></td>
              
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


  $('#uploadedreporte').click(function(){

    $.ajax({
      url:'postfolder/export.php',
      type:'post',
      data:'uploadedreporte=ok',
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