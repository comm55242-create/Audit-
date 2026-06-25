<?php include('header.php'); ?>
<?php confirm_logged_in(); 
$sc = $_SESSION['storecode'];
?>

 
        <div id="content" class="col-lg-10 col-sm-10">
            <!-- content starts -->
           <div class="box-inner">
            <div class="box-header well">
                <h2><i class="glyphicon glyphicon-info-sign"></i> Audit Report on: <?php echo $_SESSION['storecode'];?></h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round btn-default"><i
                            class="glyphicon glyphicon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round btn-default"><i
                            class="glyphicon glyphicon-remove"></i></a>
                </div>
            </div>
            <div class="box-content row"> 
          <?php 
		  $staff = $_SESSION['staff_id'];
		    $sql = oci_parse($conn, "SELECT * FROM HEAD_AUDIT WHERE EMP_CODE = '{$staff}' AND SHOP_CODE = '{$sc}'");
			oci_execute($sql);
 ?>
 
 <div class="fluid-container">
 <table class="table table-striped table-bordered bootstrap-datatable datatable responsive">
    <thead>
    <tr>
        <th>Audit Number</th>
        <th>Item Code</th>
        <th>Qty</th>
        <th>Date/Time</th>
       
    </tr>
    </thead>
    <tbody>
    <?php $count = 1; while ($trica = oci_fetch_array($sql)){
$tinto = $trica['ITEM_CODE'];
$Qty = $trica['QTY'];
$Date = $trica['DATE_SYS'];?>
    <tr>
        <td><?php echo $count; ?></td>
        <td class="center"><?php echo $tinto; ?></td>
        <td class="center"><?php echo $Qty; ?></td>
        <td class="center">
            <?php echo $Date; ?>
        </td>
       
    </tr><?php $count++;}?>
    </tbody>
    </table>
</div>  
 </div></div>
              
    <!-- content ends -->
    </div><!--/#content.col-md-0-->
</div><!--/fluid-row-->

    <!-- Ad, you can remove it --> 

    </div>
    <!-- Ad ends -->

    <hr>

<?php require('footer.php'); ?>
