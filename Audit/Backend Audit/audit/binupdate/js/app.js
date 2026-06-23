$(document).ready(function() {
	var bc_input = $('#barcode'),
		bin_input = $('#bin'),
		inputs = $('#barcode, #bin'),
		feedback = $('#feedback');
	// focus barcode input on launch
	bc_input.select();

	$("#submit").click(function(event) {
		event.preventDefault();
		$("table td").each(function() {
			$(this).text('');
		});
		var proceed = true;
		// check for empty inputs
		inputs.each(function(){
			if ($(this).val() === "") {
				$(this).select();
				proceed = false;
			}
		});
		feedback.removeClass('bg-success', 'bg-danger').empty();
		// all good? proceed with submission
		if (proceed) {
			// inputs.each(function() {
			// 	$(this).blur();
			// });
			var raw = bc_input.val().split(',');
			if (raw[raw.length-1] === "") {
				raw.pop();
			}
			data = {
				zone: $.trim(raw[0]),
				gnr_no: $.trim(raw[1]),
				item_code: $.trim(raw[2]),
				qty: $.trim(raw[3]),
				pallet: $.trim(raw[4]),
				bin: $.trim(bin_input.val())
			};
			// send data object to server script
			$.post('process.php', data, function(response) {
				feedback.text(response).addClass("bg-success");
				$('table td').each(function(index) {
					$(this).text(data[Object.keys(data)[index]]);
				});
			}).fail(function(error) {
				feedback.text(error.responseText).addClass("bg-danger");
			});
			// empty input fields after submission and focus on barcode for next input
			inputs.each(function() {
				$(this).val('');
			});
			bc_input.select();
		}

	});

});