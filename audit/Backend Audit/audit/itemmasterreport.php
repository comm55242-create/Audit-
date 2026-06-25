
<?php include('header.php'); ?>
<?php
confirm_logged_in();
$SHOP_CODE = $_SESSION['storecode'];
$rack_id = $_SESSION['rack_number'];
?>

<style>
    
    #ealertdiv{
      display: none;
    }
    .exportbutton{
    	margin-bottom:30px;
    }
</style>

<div>
	<ol class="breadcrumb">
		<li><a href="#">Home</a></li>
		<li><a href="#">Item Master Report</a></li>
	</ol>
</div>

<div id="content" class="col-lg-12">
  <!-- content starts -->
	<div class="box-inner" style="padding:10px">
		

		<div>

			<h3>Select the report you want to view</h3>

			<div class='text-center'>
				<a href="itemmasterreport.php?type=tobescanned" title="Items to be scanned" data-toggle="tooltip" class="btn btn-info"

					<?= (isset($_GET['type']) && $_GET['type'] == 'tobescanned') ? 'disabled':'' ?>
				>Items to be scanned</a>

				<a href="itemmasterreport.php?type=zerostock" title="Items without stock" data-toggle="tooltip" class="btn btn-info"

					<?= (isset($_GET['type']) && $_GET['type'] == 'zerostock') ? 'disabled':'' ?>
				>Items without stock</a>

				<a href="itemmasterreport.php?type=category" title="Category Wise" data-toggle="tooltip" class="btn btn-info"
					style="<?= (isset($_GET['category'])) ? 'opacity: .8':'' ?>"
					<?= (isset($_GET['type']) && $_GET['type'] == 'category') ? 'disabled':'' ?>
				>Category Wise</a>

				<a href="itemmasterreport.php?type=allitems" title="All items" data-toggle="tooltip" class="btn btn-info"
					<?= (isset($_GET['type']) && $_GET['type'] == 'allitems') ? 'disabled':'' ?>
				>All items</a>

				<a href="itemmasterreport.php?type=notallowed" title="Items not allowed in the PI" data-toggle="tooltip" class="btn btn-info"

					<?= (isset($_GET['type']) && $_GET['type'] == 'notallowed') ? 'disabled':'' ?>
				>Items not allowed in the PI</a>
			</div>


			<?php 


				if(isset($_GET['type'])){ 

					if($_GET['type'] == 'tobescanned'){ 


						$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND CURR_STOCK <> 0 AND CH_PI = 'N' ORDER BY DEPT ");
      					oci_execute($sql);
      		?>
      			<div style='margin-top:50px'>

      				<button id='tobescannede' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

      				<div id='ealertdiv' class="alert alert-success">
				        <button type="button" class="close" data-dismiss="alert">×</button>
				        <strong>Export successful on!</strong> 
				        <a id='dfile'
				          	href="" 
				          	download = ""> 
				        </a>
				    </div>

					<table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
						<thead>
							<tr>
								<th>No</th>
								<th>ITEM CODE</th>
								<th>ITEM NAME</th>
								<th>PRICE</th>
								<th>ERP QTY</th>
								<th>DEPT</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							while ($trica = oci_fetch_array($sql)) {

							$ITEM_CODE = $trica['ITEM_CODE'];
							$ITEM_NAME = $trica['ITEM_NAME'];
							$ERP_QTY = $trica['CURR_STOCK'];
							$DEPT = $trica['DEPT'];
							$PRICE = $trica['PRICE'];
							?>

							<tr>
								<td><?php echo $count; ?></td>
								<td class="center"><?php echo $ITEM_CODE; ?></td>
								<td class="center"><?php echo $ITEM_NAME; ?></td>
								<td class="center"><?php echo $PRICE; ?></td>
								<td class="center"><?php echo $ERP_QTY; ?></td>
								<td class="center"><?php echo $DEPT; ?></td>

							</tr><?php $count++;

							} ?>
						</tbody>
					</table>

				</div>

      		<?php

					} 

					else if($_GET['type'] == 'zerostock'){ 


						$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND CURR_STOCK = 0 AND CH_PI = 'N' ORDER BY DEPT ");
      					oci_execute($sql);
      		?>
      			<div style='margin-top:50px'>

      				<button id='zerostocke' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

      				<div id='ealertdiv' class="alert alert-success">
				        <button type="button" class="close" data-dismiss="alert">×</button>
				        <strong>Export successful on!</strong> 
				        <a id='dfile'
				          	href="" 
				          	download = ""> 
				        </a>
				    </div>

					<table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
						<thead>
							<tr>
								<th>No</th>
								<th>ITEM CODE</th>
								<th>ITEM NAME</th>
								<th>PRICE</th>
								<th>ERP QTY</th>
								<th>DEPT</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							while ($trica = oci_fetch_array($sql)) {

							$ITEM_CODE = $trica['ITEM_CODE'];
							$ITEM_NAME = $trica['ITEM_NAME'];
							$ERP_QTY = $trica['CURR_STOCK'];
							$DEPT = $trica['DEPT'];
							$PRICE = $trica['PRICE'];
							?>

							<tr>
								<td><?php echo $count; ?></td>
								<td class="center"><?php echo $ITEM_CODE; ?></td>
								<td class="center"><?php echo $ITEM_NAME; ?></td>
								<td class="center"><?php echo $PRICE; ?></td>
								<td class="center"><?php echo $ERP_QTY; ?></td>
								<td class="center"><?php echo $DEPT; ?></td>

							</tr><?php $count++;

							} ?>
						</tbody>
					</table>

				</div>

      		<?php

					}

					else if($_GET['type'] == 'notallowed'){ 


						$sql = oci_parse($conn, "SELECT * FROM MASTER_ITEM WHERE CH_PI = 'Y' ");
      					oci_execute($sql);
      		?>
      			<div style='margin-top:50px'>

      				<button id='notallowede' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

      				<div id='ealertdiv' class="alert alert-success">
				        <button type="button" class="close" data-dismiss="alert">×</button>
				        <strong>Export successful on!</strong> 
				        <a id='dfile'
				          	href="" 
				          	download = ""> 
				        </a>
				    </div>

					<table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
						<thead>
							<tr>
								<th>No</th>
								<th>ITEM CODE</th>
								<th>ITEM NAME</th>
								<th>PRICE</th>
								<th>ERP QTY</th>
								<th>DEPT</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							while ($trica = oci_fetch_array($sql)) {

							$ITEM_CODE = $trica['ITEM_CODE'];
							$ITEM_NAME = $trica['ITEM_NAME'];
							$ERP_QTY = $trica['CURR_STOCK'];
							$DEPT = $trica['DEPT'];
							$PRICE = $trica['PRICE'];
							?>

							<tr>
								<td><?php echo $count; ?></td>
								<td class="center"><?php echo $ITEM_CODE; ?></td>
								<td class="center"><?php echo $ITEM_NAME; ?></td>
								<td class="center"><?php echo $PRICE; ?></td>
								<td class="center"><?php echo $ERP_QTY; ?></td>
								<td class="center"><?php echo $DEPT; ?></td>

							</tr><?php $count++;

							} ?>
						</tbody>
					</table>

				</div>

      		<?php

					} 

					else if($_GET['type'] == 'allitems'){ 


						$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' ORDER BY DEPT ");
      					oci_execute($sql);
      		?>
      			<div style='margin-top:50px'>

      				<button id='allitemse' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

      				<div id='ealertdiv' class="alert alert-success">
				        <button type="button" class="close" data-dismiss="alert">×</button>
				        <strong>Export successful on!</strong> 
				        <a id='dfile'
				          	href="" 
				          	download = ""> 
				        </a>
				    </div>

					<table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
						<thead>
							<tr>
								<th>No</th>
								<th>ITEM CODE</th>
								<th>ITEM NAME</th>
								<th>PRICE</th>
								<th>ERP QTY</th>
								<th>DEPT</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							while ($trica = oci_fetch_array($sql)) {

							$ITEM_CODE = $trica['ITEM_CODE'];
							$ITEM_NAME = $trica['ITEM_NAME'];
							$ERP_QTY = $trica['CURR_STOCK'];
							$DEPT = $trica['DEPT'];
							$PRICE = $trica['PRICE'];
							?>

							<tr>
								<td><?php echo $count; ?></td>
								<td class="center"><?php echo $ITEM_CODE; ?></td>
								<td class="center"><?php echo $ITEM_NAME; ?></td>
								<td class="center"><?php echo $PRICE; ?></td>
								<td class="center"><?php echo $ERP_QTY; ?></td>
								<td class="center"><?php echo $DEPT; ?></td>

							</tr><?php $count++;

							} ?>
						</tbody>
					</table>

				</div>

      		<?php

					} else if($_GET['type'] == 'category'){ 


						$sql = oci_parse($conn, "SELECT DISTINCT DEPT, COUNT(DISTINCT ITEM_CODE) ITEM_COUNT  FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}'  AND CURR_STOCK <> 0 AND CH_PI = 'N' GROUP BY DEPT ORDER BY DEPT ");
      					oci_execute($sql);
      		?>
      			<div style='margin-top:50px'>

					<table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
						<thead>
							<tr>
								<th>No</th>
								<th>DEPT</th>
								<th>SCANNABLE ITEMS COUNT</th>
								<th></th>
								
							</tr>
						</thead>
						<tbody>
							<?php

	      					$count = 1;
							while ($trica = oci_fetch_array($sql)) {

							$DEPT = $trica['DEPT'];
							$ITEM_COUNT = $trica['ITEM_COUNT'];
							
							?>
							<tr>
								<td><?php echo $count; ?></td>
								<td><?= $DEPT ?></td>
								<td><?= $ITEM_COUNT ?></td>
								<td> 
									<a href='itemmasterreport.php?type=categorydata&category=<?= urlencode($DEPT) ?>' class="btn btn-info btn-sm" href=''>View list</a>
									<a href='itemmasterreport.php?type=categorydatazero&category=<?= urlencode($DEPT) ?>' class="btn btn-info btn-sm" href=''>View list with 0 qty</a>
								</td>

							</tr>

							<?php $count++;

							} ?>
						</tbody>
					</table>

				</div>

      		<?php
      		

					} else if($_GET['type'] == 'categorydata'){ 

						$category = $_GET['category'];
						
						

						$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND DEPT = '{$category}' AND CURR_STOCK <>0 AND CH_PI = 'N' ORDER BY ITEM_CODE ");
      					oci_execute($sql);

      		?>
      			<div style='margin-top:50px'>

      				<h4 style="margin:20px;" class='text-info'><u>Category:</u> <span id='categorySpan' data-cat="<?= urlencode($category) ?>"><?= $category ?></span></h4>

      				<button id='categorye' class="btn btn-primary exportbutton" ><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export Csv</button>

      				<div id='ealertdiv' class="alert alert-success">
				        <button type="button" class="close" data-dismiss="alert">×</button>
				        <strong>Export successful on!</strong> 
				        <a id='dfile'
				          	href="" 
				          	download = ""> 
				        </a>
				    </div>

					<table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
						<thead>
							<tr>
								<th>No</th>
								<th>ITEM CODE</th>
								<th>ITEM NAME</th>
								<th>PRICE</th>
								<th>ERP QTY</th>
								<th>DEPT</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							while ($trica = oci_fetch_array($sql)) {

							$ITEM_CODE = $trica['ITEM_CODE'];
							$ITEM_NAME = $trica['ITEM_NAME'];
							$ERP_QTY = $trica['CURR_STOCK'];
							$DEPT = $trica['DEPT'];
							$PRICE = $trica['PRICE'];
							?>

							<tr>
								<td><?php echo $count; ?></td>
								<td class="center"><?php echo $ITEM_CODE; ?></td>
								<td class="center"><?php echo $ITEM_NAME; ?></td>
								<td class="center"><?php echo $PRICE; ?></td>
								<td class="center"><?php echo $ERP_QTY; ?></td>
								<td class="center"><?php echo $DEPT; ?></td>

							</tr><?php $count++;

							} ?>
						</tbody>
					</table>

				</div>

      		<?php

					} else if($_GET['type'] == 'categorydatazero'){ 

						$category = $_GET['category'];
						
						

						$sql = oci_parse($conn, "SELECT DISTINCT ITEM_CODE, ITEM_NAME, PRICE, CURR_STOCK, DEPT FROM MASTER_ITEM WHERE SHOP_CODE = '{$SHOP_CODE}' AND DEPT = '{$category}' ORDER BY ITEM_CODE ");
      					oci_execute($sql);

      		?>
      			<div style='margin-top:50px'>

      				<h4 style="margin:20px;" class='text-info'><u>Category:</u> <?= $category ?></h4>


					<table id ='example' class="display table table-striped table-bordered bootstrap-datatable datatable">
						<thead>
							<tr>
								<th>No</th>
								<th>ITEM CODE</th>
								<th>ITEM NAME</th>
								<th>PRICE</th>
								<th>ERP QTY</th>
								<th>DEPT</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							while ($trica = oci_fetch_array($sql)) {

							$ITEM_CODE = $trica['ITEM_CODE'];
							$ITEM_NAME = $trica['ITEM_NAME'];
							$ERP_QTY = $trica['CURR_STOCK'];
							$DEPT = $trica['DEPT'];
							$PRICE = $trica['PRICE'];
							?>

							<tr>
								<td><?php echo $count; ?></td>
								<td class="center"><?php echo $ITEM_CODE; ?></td>
								<td class="center"><?php echo $ITEM_NAME; ?></td>
								<td class="center"><?php echo $PRICE; ?></td>
								<td class="center"><?php echo $ERP_QTY; ?></td>
								<td class="center"><?php echo $DEPT; ?></td>

							</tr><?php $count++;

							} ?>
						</tbody>
					</table>

				</div>

      		<?php

					}
				}
			?>
		</div>

      
	</div>
</div><!--box-inner-->


<hr>

<?php require('footer.php'); ?>

<script type="text/javascript">
	
	$('#tobescannede').click(function(){

		$.ajax({
			url:'postfolder/export.php',
			type:'post',
			data:'tobescannede=ok',
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

	$('#zerostocke').click(function(){

		$.ajax({
			url:'postfolder/export.php',
			type:'post',
			data:'zerostocke=ok',
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

	$('#allitemse').click(function(){

		$.ajax({
			url:'postfolder/export.php',
			type:'post',
			data:'allitemse=ok',
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

	$('#notallowede').click(function(){

		$.ajax({
			url:'postfolder/export.php',
			type:'post',
			data:'notallowede=ok',
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

	$('#categorye').click(function(){

		var category = $('#categorySpan').attr('data-cat')

		$.ajax({
			url:'postfolder/export.php',
			type:'post',
			data:'categorye=ok&cat='+category,
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
