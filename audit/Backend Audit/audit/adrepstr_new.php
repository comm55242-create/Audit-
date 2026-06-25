<?php include('header.php'); ?>
<?php confirm_logged_in(); ?>

<style>
    #cancel{
      display: none;
    }
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
    #linkText{
      font-weight: bold;
      font-size: 18px;
      margin-bottom: 30px;
      text-align: center;
      display: none;
    }
    #linkText a{
      display: inline-block;
      text-decoration: underline;
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
		<li>Final report</li>
	</ol>
    <div class="box-content row"> 
      <p>
        <a style='opacity:.5' class="btn btn-success" href="<?php echo $_SERVER['PHP_SELF']; ?>?not=2"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv to  upload</a>

        <button id='exportcdiscreport' class="btn btn-primary" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

        <button id='upload' class='btn btn-info'> Upload</button>
        <button id='cancel' class='btn btn-danger'> Cancel</button>

        <span id='itemcount' style="display:inline-block;margin-left: 20px;font-weight: bold; font-size: 20px;color: black;"></span>
    
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
      <div id='linkText'>
        You can now view the <a target='_blank' href='uploaded_items.php'>uploaded items report</a>
      </div>  
      <?php
      $sql = oci_parse($conn, "SELECT * FROM ZS_STOCK_AUDIT_ERP_NEW WHERE VC_SHOP_CODE ='{$cant}'");
      oci_execute($sql);
      ?>
      <table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
        <thead>
          <tr>
            <th>Audit Number</th>
            <th>Shop Code</th>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>ITEM PRICE</th>
            <th>Audit Qty</th>
            <th>DEPT</th>

          </tr>
        </thead>
        <tbody>
          <?php
          $count = 1;
          while ($trica = oci_fetch_array($sql)) {
            $VC_SHOP_CODE = $trica['VC_SHOP_CODE'];
            $SHOP_CODE = $trica['VC_SHOP_CODE'];
            $ITEM_CODE = $trica['VC_ITEM_CODE'];
            $ITEM_NAME = $trica['ITEM_NAME'];
            $AUDIT_QTY = $trica['VC_AUDIT_QTY'];
            $DEPT = $trica['DEPT'];
            $PRICE = $trica['PRICE'];
            
            ?>
            <tr>
              <td><?php echo $count; ?></td>
              <td class="center"><?php echo $VC_SHOP_CODE; ?></td>
              <td class="center"><?php echo $ITEM_CODE; ?></td>
              <td class="center"><?php echo $ITEM_NAME; ?></td>
              <td class="center"><?php echo $PRICE; ?></td>
              <td class="center"><?php echo $AUDIT_QTY; ?></td>
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
      data:'finalreport=ok',
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