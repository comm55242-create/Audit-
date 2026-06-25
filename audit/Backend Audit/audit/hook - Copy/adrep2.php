<?php include('header.php'); ?>
<?php confirm_logged_in(); ?>
<?php $gotru = trim($_GET['tanto']);
		$sc = $_SESSION['storecode'];
if(empty($gotru)){
	redirect_to("index.php");
	}
	
	if(isset($_GET['not'])){
		$sql = oci_parse($conn, "SELECT ITEM_CODE, SHOP_CODE, QTY, DATE_SYS FROM HEAD_AUDIT WHERE SHOP_CODE = '{$sc}'");
		oci_execute($sql);
		$dtime = date('Y-m-d H,i,s');
		$dron2 = 'C:\report\books'.$gotru.' '.$dtime.'.csv';
    $fp = fopen($dron2, 'w');
 while(($trica = oci_fetch_array($sql)) && ($row = oci_fetch_assoc($sql)))
    {
		$trouble = $trica['ITEM_CODE'];
		$Qty = $trica['QTY'];
		$Date = $trica['DATE_SYS'];
		$trat = oci_parse($conn, "SELECT * FROM MASTER_ITEM WHERE DEPT = '{$gotru}' AND ITEM_CODE = '{$trouble}'");
		oci_execute($trat);
			
		$richard = oci_fetch_array($trat);
		$tinto = $richard['ITEM_CODE'];
		if($trouble == $tinto){
			
			fputcsv($fp, $row);
		}else{
			
		}
    }
		?>
    <div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>Export Successfull on!</strong> <a href="<?php echo 'C:\report\books'.$gotru.' '.$dtime.'.csv'; ?>" download = "<?php echo 'books'.$gotru;?>"><?php echo 'C:\report\books'.$gotru.' '.$dtime.'.csv'; ?></a>
			  </div> 
   <?php fclose($fp);

    //close the db connection
    //mysql_close();
	
}

?>
    <div id="content" class="col-lg-10 col-sm-10">
            <!-- content starts -->
           <div class="box-inner">
            <div class="box-header well">
                <h2><i class="glyphicon glyphicon-info-sign"></i> Audit Report on: <?php echo $_SESSION['storecode'];?> in <?php echo $gotru;?> Department.</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round btn-default"><i
                            class="glyphicon glyphicon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round btn-default"><i
                            class="glyphicon glyphicon-remove"></i></a>
                </div>
            </div>
            <div class="box-content row"> <p><a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF'];?>?not=2&tanto=<?php echo $gotru;?>"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</a></p>     
          <?php 
		 
		    
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
		$sc = $_SESSION['storecode'];
		$sql = oci_parse($conn, "SELECT * FROM HEAD_AUDIT WHERE SHOP_CODE = '{$sc}'");
		oci_execute($sql);
	$count = 1; 
	while ($trica = oci_fetch_array($sql)){
		$trouble = $trica['ITEM_CODE'];
		$Qty = $trica['QTY'];
		$Date = $trica['DATE_SYS'];
		$trat = oci_parse($conn, "SELECT * FROM MASTER_ITEM WHERE DEPT = '{$gotru}' AND ITEM_CODE = '{$trouble}'");

		oci_execute($trat);
			
		$richard = oci_fetch_array($trat);
		$tinto = $richard['ITEM_CODE'];

		if($trouble == $tinto){
?>

    <tr>
        <td><?php echo $count; ?></td>
        <td class="center"><?php echo $tinto; ?></td>
        <td class="center"><?php echo $Qty; ?></td>
        <td class="center">
            <?php echo $Date; ?>
        </td>
       
    </tr><?php $count++;}else {
		
		} 
}?>
    </tbody>
    </table>
              
 </div></div>
              
    <!-- content ends -->
    </div><!--/#content.col-md-0-->
</div><!--/fluid-row-->

    <!-- Ad, you can remove it --> 

    </div>
    <!-- Ad ends -->

    <hr>

<?php require('footer.php'); ?>
