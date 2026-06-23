var audit_app = angular.module('auditActivity', []);

audit_app.controller('mainCtrl', function($scope, $http){
	$scope.search = {};
	$scope.alertMessage = null;
	$scope.getItem = function() {
		$scope.showQty = false;
		$http.post('audit_activity_api.php', $scope.search)
		.then(function(response) {
			$scope.searchResult = response.data;
			$scope.alertMessage = "Item Found";
			$scope.alertClass = "success";
			$scope.displayResult = $scope.alert = true;
		}, function(err) {
			$scope.alertMessage = err.data;
			$scope.alertClass = "danger";
			$scope.displayResult = false;
			$scope.alert = true;
		});
	};
	$scope.updateQty = function() {
		data = {
			qty: $scope.qty,
			barcode: $scope.searchResult.barcode,
			item_code: $scope.searchResult.item_code,
			rack_num: $scope.searchResult.rack_num
		};
		$http.post('audit_activity_api.php', data)
		.then(function(response) {
			$scope.alertMessage = response.data;
			$scope.alertClass = "success";
			$scope.showQty = true;
		}, function(err) {
			$scope.alertMessage = err.data;
			$scope.alertClass = "danger";
			$scope.alert = true;
		});
	};
	$scope.clear = function() {
		$scope.alert = $scope.displayResult = $scope.showQty = false;
		$scope.search.rack = $scope.search.barcode = $scope.qty = $scope.searchResult = null;
	};
	$scope.update = function() {
		if ($scope.searchResult.confirmation) {
			$('#confirmUpdate').modal({
				backdrop: 'static', 
				keyboard: false
			});
		} else {
			$scope.updateQty();
		}
	};

});