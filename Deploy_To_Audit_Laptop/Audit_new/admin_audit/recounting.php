<?php include('header.php'); ?>
<?php
confirm_logged_in();
$SHOP_CODE = $_SESSION['storecode'];
?>

<div>
  <ol class="breadcrumb">
    <li><a href="index.php">Home</a></li>
    <li><a href="#">Recounting</a></li>
  </ol>
</div>

<div id="content" class="col-lg-12">
  
  <!-- CSV Upload Section -->
  <div class="box-inner" style="margin-bottom: 20px;">
    <div class="box-header well">
      <h2 style="font-size: 13px;"><i class="glyphicon glyphicon-file"></i> Bulk CSV Recount Upload</h2>
    </div>
    <div class="box-content row"> 
      <form class="form-horizontal container" method="post" name="frm_csv">
        <br>
        <div class="form-group">
          <div class="col-sm-12" style="display: flex; gap: 10px; align-items: center;">
            <iframe name="hidden_download_iframe" style="display:none;"></iframe>
            <a href="download_csv_format.php" target="hidden_download_iframe" class="btn btn-success" style="height: 34px;">Download Format</a>
            
            <input type="file" id="csv_file_input" name="csv_file" accept=".csv" class="form-control" style="width: auto; padding: 5px; height: 34px;" onchange="document.getElementById('upload_csv_btn').disabled = !this.value;" />
            
            <button type="submit" id="upload_csv_btn" disabled name="upload_csv" formaction="update.php?un=<?php echo urlencode($_SESSION['username']); ?>" formenctype="multipart/form-data" class="btn btn-info" style="height: 34px;">Upload CSV</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Manual Entry Section -->
  <div class="box-inner">
    <div class="box-header well">
      <h2 style="font-size: 13px;"><i class="glyphicon glyphicon-pencil"></i> Manual Recount Entry</h2>
    </div>
    <div class="box-content row" style="padding: 20px;"> 
      
      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-heading">Enter Item</div>
          <div class="panel-body">
            <div class="form-group">
              <label>Item Code / Barcode:</label>
              <input type="text" id="recount_item_code" class="form-control" autofocus autocomplete="off">
            </div>
            <div class="form-group">
              <label>Quantity:</label>
              <input type="number" id="recount_qty" class="form-control" disabled step="any">
            </div>
            <button type="button" id="btn_add_recount" class="btn btn-primary" disabled style="width: 100%;">Add to Pending List</button>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="panel panel-info">
          <div class="panel-heading">Item Details</div>
          <div class="panel-body" id="item_details_panel" style="min-height: 175px;">
            <p class="text-muted">Type an item code to view details...</p>
          </div>
        </div>
      </div>

      <!-- Pending List -->
      <div class="col-md-12" style="margin-top: 20px;">
        <h4>Pending Recounts</h4>
        <form method="post" action="update.php?un=<?php echo urlencode($_SESSION['username']); ?>">
          <table class="table table-bordered table-striped" id="pending_recounts_table">
            <thead>
              <tr>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <!-- JS will inject rows here -->
            </tbody>
          </table>
          <div id="hidden_inputs_container"></div>
          
          <button type="submit" id="btn_submit_all" name="submit_manual_recounts" class="btn btn-success" style="display:none;">Submit All Recounts</button>
        </form>
      </div>

    </div>
  </div>

</div><!--/#content.col-md-12-->

<hr>

<script>
$(document).ready(function() {


    let pendingList = [];
    let currentItem = null;
    let debounceTimer;

    $('#recount_item_code').on('input', function() {
        clearTimeout(debounceTimer);
        let code = $(this).val().trim();
        
        if (code.length >= 3) {
            debounceTimer = setTimeout(function() {
                $('#item_details_panel').html('<p class="text-warning">Searching...</p>');
                
                $.ajax({
                    url: 'ajax_get_item.php',
                    type: 'GET',
                    data: { item_code: code },
                    dataType: 'json',
                    success: function(response) {
                        if (response.valid) {
                            currentItem = response;
                            let html = `
                                <h4><strong style="color: #2c3e50;">${response.item_name}</strong></h4>
                                <p><strong>Item Code:</strong> <span class="label label-default">${response.item_code}</span></p>
                                <p><strong>Dept:</strong> ${response.dept}</p>
                                <p><strong>Price:</strong> ${response.price}</p>
                                <p><strong>System Stock:</strong> ${response.stock}</p>
                            `;
                            $('#item_details_panel').html(html);
                            $('#recount_qty').prop('disabled', false).focus();
                        } else {
                            currentItem = null;
                            $('#item_details_panel').html('<p class="text-danger">Item not found in MASTER_ITEM.</p>');
                            $('#recount_qty').prop('disabled', true).val('');
                            $('#btn_add_recount').prop('disabled', true);
                        }
                    },
                    error: function() {
                        $('#item_details_panel').html('<p class="text-danger">Error connecting to server.</p>');
                    }
                });
            }, 400); // 400ms debounce
        } else {
            currentItem = null;
            $('#item_details_panel').html('<p class="text-muted">Type an item code to view details...</p>');
            $('#recount_qty').prop('disabled', true).val('');
            $('#btn_add_recount').prop('disabled', true);
        }
    });

    $('#recount_qty').on('input', function() {
        if ($(this).val() !== '') {
            $('#btn_add_recount').prop('disabled', false);
        } else {
            $('#btn_add_recount').prop('disabled', true);
        }
    });

    $('#recount_item_code, #recount_qty').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            if (!$('#btn_add_recount').prop('disabled')) {
                $('#btn_add_recount').click();
            }
        }
    });

    $('#btn_add_recount').on('click', function() {
        if (!currentItem) return;
        
        let qty = $('#recount_qty').val();
        if (qty === '') return;

        // Add to array
        let index = pendingList.length;
        pendingList.push({
            item_code: currentItem.item_code,
            item_name: currentItem.item_name,
            quantity: qty
        });

        // Add to UI Table
        let tr = `
            <tr id="row_${index}">
                <td>${currentItem.item_code}</td>
                <td>${currentItem.item_name}</td>
                <td>${qty}</td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRecount(${index})">Remove</button></td>
            </tr>
        `;
        $('#pending_recounts_table tbody').append(tr);

        // Add to hidden form
        let inputs = `
            <input type="hidden" name="recounts[${index}][item_code]" id="hidden_code_${index}" value="${currentItem.item_code}">
            <input type="hidden" name="recounts[${index}][quantity]" id="hidden_qty_${index}" value="${qty}">
            <input type="hidden" name="recounts[${index}][item_name]" id="hidden_name_${index}" value="${currentItem.item_name}">
        `;
        $('#hidden_inputs_container').append(inputs);

        // Reset UI
        $('#recount_item_code').val('').focus();
        $('#recount_qty').val('').prop('disabled', true);
        $('#btn_add_recount').prop('disabled', true);
        $('#item_details_panel').html('<p class="text-muted">Type an item code to view details...</p>');
        currentItem = null;

        updateSubmitBtn();
    });

    window.removeRecount = function(index) {
        $('#row_' + index).remove();
        $('#hidden_code_' + index).remove();
        $('#hidden_qty_' + index).remove();
        $('#hidden_name_' + index).remove();
        delete pendingList[index];
        updateSubmitBtn();
    };

    function updateSubmitBtn() {
        let hasItems = Object.values(pendingList).some(item => item !== undefined);
        if (hasItems) {
            $('#btn_submit_all').show();
        } else {
            $('#btn_submit_all').hide();
        }
    }
});
</script>

<?php require('footer.php'); ?>
