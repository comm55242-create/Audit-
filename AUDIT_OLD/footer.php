<?php
require_once("includes/connection.php");
?>
<?php if (!isset($no_visible_elements) || !$no_visible_elements) { ?>
<!-- content ends--> 
</div><!--/#content.col-md-0-->
<?php } ?>
</div><!--/fluid-row-->
<?php if(!isset($no_visible_elements) || !$no_visible_elements) { ?>
	<hr>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" 
aria-hidden="true">
    <div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span class="glyphicon glyphicon-minus-sign"></span></button>
				<h4>Details</h4>
			</div>
		<div class="modal-body">
				<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<?php
				$rita3 = $_SESSION['storecode'];
				$rita = $_SESSION['staff_id'];
				$add = oci_parse($conn, "SELECT ADMIN FROM USERS WHERE STAFF_ID = '{$rita}'");
				oci_execute($add);
				$ytu = oci_fetch_array($add);
	
				if ($ytu['ADMIN'] == '1' || $_SESSION['staff_id'] === 'ADMIN') {
				?>
				<li role="presentation" class="active"><a href="#dropdown1" aria-controls="dropdown1" role="tab" data-toggle="tab">Audit Report</a></li>
				<li role="presentation"><a href="#messages23" aria-controls="messages23" role="tab" data-toggle="tab">View Reports</a></li>
				<li role="presentation"><a href="#dropdown3" aria-controls="dropdown3" role="tab" data-toggle="tab">Pending Stock Take</a></li>
				<li role="presentation"><a href="#messages24" aria-controls="messages24" role="tab" data-toggle="tab">Provisional Discrepancy Report</a></li>
				<li role="presentation"><a href="#messages37" aria-controls="messages37" role="tab" data-toggle="tab">Consolidated Discrepancy Report</a></li>
				<li role="presentation"><a href="#messages27" aria-controls="messages27" role="tab" data-toggle="tab">Audit Report 2</a></li>
				<li role="presentation"><a href="#messages25" aria-controls="messages25" role="tab" data-toggle="tab"><?php echo $rita3; ?> Stock Audit Report ERP</a></li>
				<li role="presentation"><a href="#messsage26" aria-controls="messsage26" role="tab" data-toggle="tab">Final Report</a></li>
				<li role="presentation"><a href="#messages38" aria-controls="messages38" role="tab" data-toggle="tab">Recounting Report</a></li>
				<li role="presentation"><a href="#messages39" aria-controls="messages39" role="tab" data-toggle="tab">Variance Report</a></li>
				<?php } ?>
			</ul>
          <!-- Tab panes -->
			<div class="tab-content">
				<!-- Removed Profile, Reports, Staffs Report, and Departme. Report tabs as requested -->
					<div role="tabpanel" class="tab-pane" id="messages23"><br>
						<a href="adrepall.php?addon=default" title="View All Report For Today" data-toggle="tooltip" class="btn btn-info">View Reports</a>
						<button class="btn btn-info exportbutton-direct" data-action="viewreportse" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
					</div>
					<div role="tabpanel" class="tab-pane active" id="dropdown1"><br>
						<a href="adrepall.php?addon=yes" title="View All Report For Today" data-toggle="tooltip" class="btn btn-info">View Audit Report</a>
						<button class="btn btn-info exportbutton-direct" data-action="auditfirstrount" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
					</div>
					<div role="tabpanel" class="tab-pane" id="dropdown4"><br><a href="adrepall.php?addon=audit2" title="Second Round Report for Today" data-toggle="tooltip" class="btn btn-info">Round 2 Report</a></div>
					<?php
					$sql = oci_parse($conn, "SELECT DISTINCT DEPT FROM ZS_VW_AUDIT_PENDING");
					if (!$sql) {
					$e2 = oci_error($conn);
					trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
					}
					$r2 = oci_execute($sql);
					?>
					<div role="tabpanel" class="tab-pane" id="dropdown3">
					<form method="POST" action="adrepall.php">
						<div class="form-group">
							<label for="inputSelectDepartment">Select Department</label>
							<select name="pending_department" class="form-control">
							<option>All</option>                    
							<?php while ($row = oci_fetch_array($sql)): ?>
							<option><?php echo $row['DEPT']; ?></option>
							<?php endwhile; ?>
							</select>
						</div><!--form-group-->
						<input name="pending_stock" type="submit" value="View Pending Stock Take" class="btn btn-info">
						<button type="button" class="btn btn-info exportbutton-direct" data-action="pendingalle" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export All CSV</button>
					</form>
					</div>
				
			<div role="tabpanel" class="tab-pane" id="messages24">
				<br>
				<a href="adrepall.php?addon=discrepancy" title="View Discrepancy Report" data-toggle="tooltip" class="btn btn-info">View Provisional Discrepancy Report</a>
				<button class="btn btn-info exportbutton-direct" data-action="provdiscreporte" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
			</div><!--tabpannel-->

			<div role="tabpanel" class="tab-pane" id="messages37">
				<br>
				<a href="adrepstr.php" title="View Stock Audit Report Today" data-toggle="tooltip" class="btn btn-info">View Consolidated Discrepancy report</a>
				<button class="btn btn-info exportbutton-direct" data-action="cdiscreport" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
			</div>
			
			<div role="tabpanel" class="tab-pane" id="messages27">
				<br>
				<a href="adrepall.php?addon=audit2" title="View audit Report" data-toggle="tooltip" class="btn btn-info">View Audit 2 round Report</a>
				<button class="btn btn-info exportbutton-direct" data-action="audit2report" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
			</div>
			
			<div role="tabpanel" class="tab-pane" id="messages25">
				<br>
				<a href="adrepstr.php" title="View Stock Audit Report Today" data-toggle="tooltip" class="btn btn-info">View Reports</a>
				<button class="btn btn-info exportbutton-direct" data-action="stockauditerp" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
			</div>
			<div role="tabpannel" class="tab-pane" id="messsage26">
				<br>
				<!-- <a href="adrepstr.php" title="View Discrepancy Report Final" data-toggle="tooltip" class="btn btn-info">View Consolidated Discrepancy Report</a> -->
				<a href="adrepstr_new.php" title="View Discrepancy Report Final" data-toggle="tooltip" class="btn btn-info">View Final Report to be uploaded</a>
				<button class="btn btn-info exportbutton-direct" data-action="finalreport" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
			</div>
			<div role="tabpanel" class="tab-pane" id="messages38">
				<br>
				<a href="adrepstr.php" title="View Recounting Report" data-toggle="tooltip" class="btn btn-info">View Recounting Report</a>
				<button class="btn btn-info exportbutton-direct" data-action="recountingreport" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
			</div>
			<div role="tabpanel" class="tab-pane" id="messages39">
				<br>
				<a href="adrepstr.php" title="View Variance Report" data-toggle="tooltip" class="btn btn-info">View Variance Report</a>
				<button class="btn btn-info exportbutton-direct" data-action="variancereport" style="margin-left:5px;"><i class="glyphicon glyphicon-cloud-download icon-white"></i> Export CSV</button>
			</div>
			</div><!--tab-content-->
		</div><!-- modal-body-->
		<div class="modal-footer">
			<a href="#" class="btn btn-danger" data-dismiss="modal">Close</a>
		</div><!--modal-footer-->
		</div><!--model-content-->
	</div><!--modal-dialog-->
</div><!--modal fade-->
  <footer class="footer">
	<div class="fluid-container">
    <p class="col-md-9 col-sm-9 col-xs-6 copyright pull-left">&copy; <a href="#" target="_blank">Melcom Group Of Companies</a> 2016 - <?php echo date('Y') ?></p>
    <p class="col-md-3 col-sm-3 col-xs-6 pull-right">Developed by : Melcom IT Team</p>
	</div><!-- container-->
  </footer>
<?php } ?>

</div><!--/.fluid-container-->

<!-- external javascript -->

<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- library for cookie management -->
<script src="js/jquery.cookie.js"></script>
<!-- calender plugin -->
<script src='bower_components/moment/min/moment.min.js'></script>
<script src='bower_components/fullcalendar/dist/fullcalendar.min.js'></script>
<!-- data table plugin -->
<script src='js/jquery.dataTables.min.js'></script>

<!-- select or dropdown enhancer -->
<script src="bower_components/chosen/chosen.jquery.min.js"></script>
<!-- plugin for gallery image view -->
<script src="bower_components/colorbox/jquery.colorbox-min.js"></script>
<!-- notification plugin -->
<script src="js/jquery.noty.js"></script>
<!-- library for making tables responsive -->
<script src="bower_components/responsive-tables/responsive-tables.js"></script>
<!-- tour plugin -->
<script src="bower_components/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
<!-- star rating plugin -->
<script src="js/jquery.raty.min.js"></script>
<!-- for iOS style toggle switch -->
<script src="js/jquery.iphone.toggle.js"></script>
<!-- autogrowing textarea plugin -->
<script src="js/jquery.autogrow-textarea.js"></script>
<!-- multiple file upload plugin -->
<script src="js/jquery.uploadify-3.1.min.js"></script>
<!-- history.js for cross-browser state change on ajax -->
<script src="js/jquery.history.js"></script>
<!-- application script for Charisma demo -->
<script src="js/charisma.js"></script>

<script>
$(document).ready(function() {
  $('.exportbutton-direct').click(function(e){
    e.preventDefault();
    var action = $(this).attr('data-action');
    var originalText = $(this).html();
    var btn = $(this);
    btn.html('Exporting...');
    $.ajax({
      url:'postfolder/export.php',
      type:'post',
      data: action + '=ok',
      dataType:'text',
      success: function(donne, status){
        var data = donne.trim();
        var link = document.createElement('a');
        link.href = 'export/' + data;
        link.download = data;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        btn.html(originalText);
      },
      error: function(){
        alert("Export failed.");
        btn.html(originalText);
      }
    });
  });
});
</script>


<script type="text/javascript" language="javascript" src="media/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" language="javascript" class="init">

<!--
//var jQuery_1_3_2 = $.noConflict(true);
  jQuery_1_3_2(document).ready(function () {
    jQuery_1_3_2('#example').DataTable({
      dom: 'T<"clear">lfrtip',
      tableTools: {
        sSwfPath: "swf/copy_csv_xls_pdf.swf"
      }
    });
  });

--></script>
<script>
  //var jQuery_1_3_2 = $.noConflict(true);		
  $('#myTabs a').click(function (e) {
    e.preventDefault()
    $(this).tab('show')
  })
</script>

<?php

$sc = $_SESSION['storecode'];

//Google Analytics code for tracking my demo site, you can remove this.
if ($_SERVER['HTTP_HOST'] == 'usman.it') {
  ?>
  <script>
    // var _gaq = _gaq || [];
    // _gaq.push(['_setAccount', 'UA-26532312-1']);
    // _gaq.push(['_trackPageview']);
    // (function () {
    //   var ga = document.createElement('script');
    //   ga.type = 'text/javascript';
    //   ga.async = true;
    //   ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    //   (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
    // })();
  </script>
<?php } ?>

<script>

	var totalNumber = 0
 
 	const beforeUnloadHandler = (event) => {
	  	event.preventDefault();
	  	event.returnValue = true;
	};
	var request = function(){
		$.ajax({
			url:'postfolder/ajaxpost.php',
			type:'post',
			data:'fetchdata=ok&sc=<?= $sc ?>',
			dataType:'text',
			success: function(donne, status){

				result = donne.trim()

	        	if(result && totalNumber){
	        		
	        		ppercent = Math.round((parseInt(result) *100)/totalNumber)
	        	
	        		$('#progress').css('width',ppercent+'%')
	        		$('#loadingSpan').text(ppercent)
	        	}
	        	
			},
			error:function(){

			}
		})
	}

	var initial = function(){

		$.ajax({
			url:'postfolder/ajaxpost.php',
			type:'post',
			data:'initialdata=ok&sc=<?= $sc ?>',
			dataType:'text',
			success: function(donne, status){
				result = donne.trim()
	        	console.log(donne)
	        	totalNumber = result
	        	$('#itemcount').text(totalNumber +" items")
			},
			error:function(){

			}
		})
	}


	var xhr;
	var working = false;

	$('#upload').click(function(){


		if(confirm('Are you sure you want to upload?')){

			$('#upload')
			.prop('disabled',true)
			.text('Uploading ...')

			// $('#cancel').show(100)
			initial()


			$('#loadingDiv').show()

			let timelyReq = setInterval(request, 1000)

			working = true
			window.addEventListener("beforeunload", beforeUnloadHandler);
			xhr = $.ajax({
				url:'postfolder/ajaxpost.php',
				type:'post',
				data:'uploadnew=ok&sc=<?= $sc ?>',
				dataType:'text',
				success: function(donne, status){
					console.log(donne);
					clearInterval(timelyReq)

					window.removeEventListener("beforeunload", beforeUnloadHandler);
		        	working = false

					if(donne.trim() == 'nerror'){
						$('#upload')
						.text('Upload failed')
			        	$('#progress').css('width','0%')
			        	$('#loadingSpan').text('Upload failed')
			        	return false;
					}

					$('#upload')
					.text('Uploaded')
		        	$('#progress').css('width','100%')
		        	$('#loadingSpan').text('Complete 100')
		        	$('#linkText').slideDown(100)
		        	window.removeEventListener("beforeunload", beforeUnloadHandler);
		        	working = false
				},
				error:function(){

				}
			})
		}

	})

	// $('#cancel').click(function(){

	// 	xhr.abort();

	// 	$('#upload')
	// 	.text('Upload')
	// 	.prop('disabled',false)

	// 	$('#cancel').hide(100)

 //    	$('#progress').css('width','0%')
 //    	$('#loadingSpan').text('0')
 //    	$('#linkText').hide(100)
 //    	window.removeEventListener("beforeunload", beforeUnloadHandler);
 //    	working = false
		
	// })
		
	window.addEventListener('unload', function(){
		xhr.abort()
		working = false
	})

	

	
	
 
</script>
	
    <script>
        window.addEventListener('load', function() {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.style.opacity = '0';
                loader.style.visibility = 'hidden';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 300);
            }
        });
        
        window.addEventListener('beforeunload', function() {
            const loader = document.getElementById('global-page-loader');
            if (loader) {
                loader.style.display = 'flex';
                loader.style.visibility = 'visible';
                loader.style.opacity = '1';
            }
        });
    </script>
</body>
</html>
