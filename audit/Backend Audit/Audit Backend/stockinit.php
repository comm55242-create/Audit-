
<?php include('header.php'); ?>
<?php
confirm_logged_in();
$SHOP_CODE = $_SESSION['storecode'];
$rack_id = $_SESSION['rack_number'];
?>
<head>
	<title>Stock Initialization</title>
</head>

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

    #animImg{
    	display:none;
    }



    #confirmModal{
    	display: none;
    	background-color: white;
    	padding:50px;
    	width: 100%;
    	position: fixed;
    	top: 0;
    	z-index: 5000;
    }

    /* The Modal (background) */
	.modalNew {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 1; /* Sit on top */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background-color: rgb(0,0,0); /* Fallback color */
		background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content/Box */
	.modal-contentNew {
		background-color: #fefefe;
		margin: 7% auto; /* 15% from the top and centered */
		padding: 30px 20px;
		border: 1px solid #888;
		width: 60%; /* Could be more or less, depending on screen size */
	}

	/* The Close Button */
	.closeNew {
		color: #aaa;
		float: right;
		font-size: 28px;
		font-weight: bold;
		opacity: 0;

	}

	.closeNew:hover,
	.closeNew:focus {
		color: black;
		text-decoration: none;
		cursor: pointer;

	}

	#confirmPop > div{
		width: 100%;
		display: flex;
		justify-content: space-between;
		margin-bottom: 10px;
		padding: 20px 10px;
    	background-color: #8080801a;

	}
	#confirmPop > div> strong{
		font-size: 20px;
	}
	#confirmPop input[type=submit]{
		display: block;
    	margin-left: auto;
    	width: 150px;
    	margin-top: 40px;
	}

	/* The switch - the box around the slider */
	.switch {
		position: relative;
		display: inline-block;
		width: 54px;
		height: 26px;
	}

	/* Hide default HTML checkbox */
	.switch input {
		opacity: 0;
		width: 0;
		height: 0;
	}

	/* The slider */
	.slider {
		position: absolute;
		cursor: pointer;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: #ccc;
		-webkit-transition: .4s;
		transition: .4s;
	}

	.slider:before {
		position: absolute;
		content: "";
		height: 20px;
		width: 20px;
		left: 4px;
		bottom: 3px;
		background-color: white;
		-webkit-transition: .4s;
		transition: .4s;
	}

	input:checked + .slider {
		background-color: #8b0000;
	}

	input:focus + .slider {
		box-shadow: 0 0 5px red;
	}

	input:checked + .slider:before {
		-webkit-transform: translateX(26px);
		-ms-transform: translateX(26px);
		transform: translateX(26px);
	}

	/* Rounded sliders */
	.slider.round {
		border-radius: 34px;
	}

	.slider.round:before {
		border-radius: 50%;
	}

	#depList{

	}

	#deptList  form >  div >div{
		display: flex;
		padding: 15px;
	    border-bottom: 1px solid #80808042;
	    justify-content: space-between;
	    align-items: center;
	}
	#deptList input[type=submit]{
    	margin-left: auto;
    	min-width: 150px;
	}
	#animImgDept{
		display: none;
	}
	#divanimsub{
		display: flex;
	    justify-content: space-between;
	    align-items: center;
	    margin-top: 50px;
	    padding: 0px 10px;
	}
	#spananimsubmit{
		display: block;
		text-align: right;
	}
	#searchDept{
		border: 1px solid #0000004a;
	    padding: 8px;
	    padding-left: 15px;
	    width: 50%;
	    border-radius: 30px;
	    margin-left: auto;
	    display: block;
	    outline: 0;
	    margin-bottom: 20px;

	}

</style>

<div>
	<ol class="breadcrumb">
		<li><a href="#">Home</a></li>
		<li><a href="#">Stock Initialization</a></li>
	</ol>
</div>

<div id="content" class="col-lg-12">
  <!-- content starts -->
	<div class="box-inner" style="padding:10px">
		

		<div>

			<h3>Choose operation</h3>

			<div class='text-center'>
				<a href="stockinit.php?type=refresh" title="Items to be scanned" data-toggle="tooltip" class="btn btn-info"

					<?= (isset($_GET['type']) && $_GET['type'] == 'refresh') ? 'disabled':'' ?>
				>Refresh stock data</a>

				<a href="stockinit.php?type=loaditemmaster" title="Load Item Master" data-toggle="tooltip" class="btn btn-info"

					<?= (isset($_GET['type']) && $_GET['type'] == 'loaditemmaster') ? 'disabled':'' ?>
				>Load Item Master</a>



				
			</div>

			<?php 


				if(isset($_GET['type'])){ 

					if($_GET['type'] == 'loaditemmaster'){ ?>




						<div style="margin: 50px;text-align: center;">

							<a style='padding:7px 10px' href="stockinit.php?type=loaditemmaster&loading=full" data-toggle="tooltip" class="btn btn-info"

								<?= (isset($_GET['loading']) && $_GET['loading'] == 'full') ? 'disabled':'' ?>
							>Full stock take</a>


							<a style='padding:7px 10px' href="stockinit.php?type=loaditemmaster&loading=dept"  data-toggle="tooltip" class="btn btn-info"

								<?= (isset($_GET['loading']) && $_GET['loading'] == 'dept') ? 'disabled':'' ?>
							>Departmental</a>

							<a style='padding:7px 10px' href="stockinit.php?type=loaditemmaster&loading=group"  data-toggle="tooltip" class="btn btn-info"

								<?= (isset($_GET['loading']) && $_GET['loading'] == 'group') ? 'disabled':'' ?>
							>Group Wise</a>

							<a style='padding:7px 10px' href="stockinit.php?type=loaditemmaster&loading=subgroup"  data-toggle="tooltip" class="btn btn-info"

								<?= (isset($_GET['loading']) && $_GET['loading'] == 'subgroup') ? 'disabled':'' ?>
							>Sub-Group Wise</a>
							
							<!-- <button id='loaditemmaster' class='btn btn-info'> Load data</button>
							<span id='loaditemmasteritemcount' style="display:inline-block;margin-left: 20px;font-weight: bold; font-size: 20px;color: black;position: relative;top: 5px"></span>
							<img id='animImg' src='media/images/anim.gif' width="45"> -->

						</div>

						

					<?php 
					} else if($_GET['type'] == 'refresh'){ ?>

						<div style="margin: 50px">
							
							<button id='refreshstockdata' class='btn btn-info'> Refresh data</button>
							

							<img id='animImg' src='media/images/anim.gif' width="45">

						</div>

						

					<?php 
					}

				} 

				if(isset($_GET['loading'])){ 

					if($_GET['loading'] == 'full'){ ?>

						<div style="margin: 50px">
							
							<button id='loaditemmaster' class='btn btn-info'> Load data</button>
							<span id='loaditemmasteritemcount' style="display:inline-block;margin-left: 20px;font-weight: bold; font-size: 20px;color: black;position: relative;top: 5px"></span>
							<img id='animImg' src='media/images/anim.gif' width="45">

						</div>

					<?php 
					}else if($_GET['loading'] == 'dept'){

						$sql = oci_parse($conn, "SELECT * FROM DEPTS WHERE DEPT NOT IN('By Products', 'Dummy', 'Fixed Assets', 'HALLAB', 'PIZZA HUT', 'Spare Parts')
								    ORDER BY DEPT ");
      					oci_execute($sql);
      					?> 

      					<div>
      						<input type='text' id='searchDept' placeholder="Search for department">

      					</div>


      					<div id='deptList'>
      						<form id='loadDept' >
      							<input type="hidden" name='loadingdeptwise'>
      							<input type="hidden" name='sc' value="<?= $SHOP_CODE ?>">
      						<div id='ddd'>
      						<?php
	      					while ($trica = oci_fetch_array($sql)) { 
	      							$DEPT = $trica['DEPT'];
	      					?>

		      						<div>
		      							<strong> <?= $DEPT ?></strong>

		      							<label class="switch">
										  	<input type="checkbox" class="deptcheck" name="depts[]" value="<?= $DEPT ?>" >
										  	<span class="slider round"></span>
										</label>
		      						</div>

		      						

							<?php } ?>

									
							</div>

								<div id='divanimsub'>
									<span id='deptscount'>0 departments selected</span>
									<span id='spananimsubmit'>
										<img id='animImgDept' src='media/images/anim.gif' width="45">
										<input type="submit" id='loaddeptdata' value="Load data" class='btn btn-info'>
									</span>
								</div>
		      				</form>

      					</div>
				
					<?php
					}else if($_GET['loading'] == 'group'){

						$sql = oci_parse($conn, "SELECT * FROM VC_GROUPS 
								    ORDER BY VC_GROUP ");
      					oci_execute($sql);
      					?> 

      					<div>
      						<input type='text' id='searchDept' placeholder="Search for department">

      					</div>


      					<div id='deptList'>
      						<form id='loadDept' >
      							<input type="hidden" name='loadinggroupwise'>
      							<input type="hidden" name='sc' value="<?= $SHOP_CODE ?>">
      						<div id='ddd'>
      						<?php
	      					while ($trica = oci_fetch_array($sql)) { 
	      							$VC_GROUP = $trica['VC_GROUP'];
	      					?>
		      						<div>
		      							<strong> <?= $VC_GROUP ?></strong>

		      							<label class="switch">
										  	<input type="checkbox" class="groupcheck" name="groups[]" value="<?= $VC_GROUP ?>" >
										  	<span class="slider round"></span>
										</label>
		      						</div>

		      						

							<?php } ?>

									
							</div>

								<div id='divanimsub'>
									<span id='deptscount'>0 departments selected</span>
									<span id='spananimsubmit'>
										<img id='animImgDept' src='media/images/anim.gif' width="45">
										<input type="submit" id='loaddeptdata' value="Load data" class='btn btn-info'>
									</span>
								</div>
		      				</form>

      					</div>
				
					<?php 
					}else if($_GET['loading'] == 'subgroup'){

						$sql = oci_parse($conn, "SELECT * FROM VC_SUBGROUPS 
								    ORDER BY VC_SUBGROUP ");
      					oci_execute($sql);
      					?> 

      					<div>
      						<input type='text' id='searchDept' placeholder="Search for department">

      					</div>


      					<div id='deptList'>
      						<form id='loadDept' >
      							<input type="hidden" name='loadingsubgroupwise'>
      							<input type="hidden" name='sc' value="<?= $SHOP_CODE ?>">
      						<div id='ddd'>
      						<?php
	      					while ($trica = oci_fetch_array($sql)) { 

	      							$VC_GROUP = $trica['VC_GROUP'];
	      							$VC_SUBGROUP = $trica['VC_SUBGROUP'];
	      					?>
		      						<div>

		      							<strong> <?= $VC_SUBGROUP ?></strong>
		      							<label class="switch">
										  	<input type="checkbox" class="subgroupcheck" name="subgroups[]" value="<?= $VC_SUBGROUP ?>" >
										  	<span class="slider round"></span>
										</label>
		      						</div>

		      						

							<?php } ?>

									
							</div>

								<div id='divanimsub'>
									<span id='deptscount'>0 departments selected</span>
									<span id='spananimsubmit'>
										<img id='animImgDept' src='media/images/anim.gif' width="45">
										<input type="submit" id='loaddeptdata' value="Load data" class='btn btn-info'>
									</span>
								</div>
		      				</form>

      					</div>
				
					<?php 
					}	
				}

				if(isset($_POST['loading'])){
					
					$deptQuery = "";
					$deptCodeArray = $_POST['depts'];

					

				}

			?>


		</div>    
	</div>
</div><!--box-inner-->


<div id="myModalNew" class="modalNew">

	<!-- Modal content -->
	<div class="modal-contentNew">
		<span class="closeNew">&times;</span>
		<div>
			
			<form id='confirmPop'>
				<h1>Stock Take Checlist</h1>

				<div>
					<strong>Have you taken back up of the required data?</strong>

					<label class="switch">
					  	<input type="checkbox" name="backup" required="">
					  	<span class="slider round"></span>
					</label>
					
				</div>

				<div>
					<strong>Have you cleared all the pending receipts?</strong>

					<label class="switch">
					  	<input type="checkbox" name="opmang" required="">
					  	<span class="slider round"></span>
					</label>
					
				</div>

				<div>
					<strong>Have you cleared all the pending issues?</strong>

					<label class="switch">
					  	<input type="checkbox" name="shop" required="">
					  	<span class="slider round"></span>
					</label>
					
					
				</div>

				
				<input type="submit" value="Approve" class='btn btn-info'>

			</form>

		</div>
	</div>

</div>
<hr>

<?php require('footer.php'); ?>

<script src="js/cookie.js"></script>
<script type="text/javascript">
	
	if(!readCookie('checklistApproved')){
		$('#myModalNew').show()
	}

	$('#confirmPop').submit(function(e){
		e.preventDefault()

		createCookie('checklistApproved', 1, 12)

		$('#myModalNew').hide()
	})

	var initialmasteritem = function(){

		$.ajax({
			url:'postfolder/ajaxpost.php',
			type:'post',
			data:'masteritemcount=ok&sc=<?= $sc ?>',
			dataType:'text',
			success: function(donne, status){
				result = donne.trim()
	        	console.log(donne)
	        	totalNumber = result
	        	$('#loaditemmasteritemcount').text(totalNumber +" items")
			},
			error:function(){

			}
		})
	}

	$('#loaditemmaster').click(function(){

		if(confirm('Are you sure you want to load the master item?')){

			$('#loaditemmaster')
			.prop('disabled',true)
			.text('Loading ...')

			$('#animImg').show()

			// initialmasteritem()

			working = true
			window.addEventListener("beforeunload", beforeUnloadHandler);

			xhr = $.ajax({
				url:'postfolder/ajaxpost.php',
				type:'post',
				data:'loaditemmaster=ok&sc=<?= $sc ?>',
				dataType:'text',
				success: function(donne, status){
					console.log(donne);

					$('#animImg').hide()

					window.removeEventListener("beforeunload", beforeUnloadHandler);
		        	working = false

					if(donne.trim() == 'ok'){

						$('#loaditemmaster')
						.text('Items loaded')
			        	
			        	window.removeEventListener("beforeunload", beforeUnloadHandler);
			        	working = false

						
					}else{

						$('#upload')
						.text('Loading failed')
					}
				},
				error:function(){

				}
			})
		}

	})

	$('#refreshstockdata').click(function(){

		if(confirm('Are you sure you want to refresh the stock data?')){
			
			$('#refreshstockdata')
			.prop('disabled',true)
			.text('Refreshing data ...')

			$('#animImg').show()

			$.ajax({
				url:'postfolder/ajaxpost.php',
				type:'post',
				data:'refreshitemmaster=ok',
				dataType:'text',
				success: function(donne, status){
					
					$('#animImg').hide()

					if(donne.trim() == 'ok'){

						$('#refreshstockdata')
						.text('Data refreshed successfully ')

					}else{

						$('#refreshstockdata')
						.text('Operation failed')
					}
				},
				error:function(){

				}
			})


		}
	})

	$('#loadDept').submit(function(e){
		e.preventDefault()

		
		$('#loaddeptdata')
		.prop('disabled',true)
		.text('Loading data ...')

		var data = $(this).serialize();

		$('#animImgDept').show()

		$.ajax({
			url:'postfolder/ajaxpost.php',
			type:'post',
			data:data,
			dataType:'text',
			success: function(donne, status){

				console.log(donne)
				$('#animImgDept').hide()

				if(donne.trim() == 'ok'){

					$('#loaddeptdata')
					.val('Data loaded successfully ')

				}else{

					$('#loaddeptdata')
					.text('Operation failed')
				}
			},
			error:function(){

			}
		})
	})

	$('#searchDept').keyup(function(){
		var value = $(this).val()

		$('#ddd div').each(function(){

			var parentDiv = $(this)
			text = parentDiv.children('strong').text()

			if(text.toUpperCase().includes(value.toUpperCase().trim())) parentDiv.show() 
			else parentDiv.hide()

			
		})
	})

	// 

	$('.deptcheck').change(function(){
		var totalChecked = $("#ddd").find('input[name="depts[]"]:checked').length;
		$('#deptscount').text(totalChecked+ ' department(s) selected')
		// console.log(total)
	})
	$('.groupcheck').change(function(){
		var totalChecked = $("#ddd").find('input[name="groups[]"]:checked').length;
		$('#deptscount').text(totalChecked+ ' group(s) selected')
		// console.log(total)
	})

	$('.subgroupcheck').change(function(){
		var totalChecked = $("#ddd").find('input[name="subgroups[]"]:checked').length;
		$('#deptscount').text(totalChecked+ ' sub-group(s) selected')
		// console.log(total)
	})

</script>
