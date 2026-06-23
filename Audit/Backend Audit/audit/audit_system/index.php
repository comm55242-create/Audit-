<?php include('header_index.php'); ?>
<?php confirm_logged_in(); 
$SHOP_CODE = $_SESSION['storecode'];
?>

<div id="content" class="col-lg-10 col-sm-10">

	<div ng-controller="mainCtrl" ng-cloak>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Audit Activity on <?php echo $_SESSION['storecode']?></h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div ng-show="alert" class="col-sm-4 col-sm-offset-4 alert alert-{{ alertClass }} text-center" role="alert">{{ alertMessage }}</div>
				</div>
				<form class="form-horizontal" ng-submit="getItem()" name="searchForm" novalidate>
					<div class="form-group">
						<label class="col-sm-4 control-label" for="rack">Rack Number</label>
						<div class="col-sm-4">
							<input ng-model="search.rack" name="rack" type="number" min="0" class="form-control input-sm" aria-describedby="rackMessage" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label" for="barcode">Item Barcode</label>
						<div class="col-sm-4">
							<input ng-model="search.barcode" name="barcode" type="text" class="form-control input-sm" aria-describedby="barcodeMessage" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
							<button ng-disabled="searchForm.$invalid" type="submit" id="search" class="btn btn-info">Search</button>
							<button ng-click="clear()" type="reset" id="clear" class="btn btn-default">Clear</button>
						</div>
					</div>
				</form>

				<table ng-show="displayResult" align="center" class="table table-bordered" id="result" style="width:auto; margin-top:50px;">
					<td class="active">{{ searchResult.item_code }}</td>
					<td class="active">{{ searchResult.item_name }}</td>
					<td class="active">{{ searchResult.barcode }}</td>
					<td class="active">{{ searchResult.price }}</td>
					<td ng-show="showQty" class="active">{{ qty }}</td>
				</table>

				<form ng-submit="update()" ng-show="displayResult" name="qtyForm" class="form-inline text-center" novalidate>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon">Quantity</div>
							<input ng-model="qty" name="qty" type="number" min="-10000" max="10000" class="form-control" required>
							<span class="input-group-btn">
								<button ng-disabled="qtyForm.$invalid" class="btn btn-info" type="submit">Save</button>
							</span>
						</div>
					</div>
				</form>

				<div class="modal fade" id="confirmUpdate" tabindex="-1" role="dialog" aria-labelledby="confirmLabel">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="confirmLabel">Confirm Update</h4>
							</div>
							<div class="modal-body">
								{{ searchResult.confirmation }}
							</div>
							<div class="modal-footer">
								<button ng-click="clear()" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<button ng-click="updateQty()" type="button" class="btn btn-primary" data-dismiss="modal">Update</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- content ends -->
</div><!--/#content.col-md-0-->
</div><!--/fluid-row-->

<!-- Ad, you can remove it --> 

</div>
<!-- Ad ends -->

<?php require('footer_index.php'); ?>
