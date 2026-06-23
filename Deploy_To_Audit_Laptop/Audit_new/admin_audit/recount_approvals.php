<?php include('header.php'); ?>
<?php
confirm_logged_in();
$SHOP_CODE = $_SESSION['storecode'];
?>

<div>
  <ol class="breadcrumb">
    <li><a href="index.php">Home</a></li>
    <li><a href="recounting.php">Recounting</a></li>
    <li><a href="#">Approvals</a></li>
  </ol>
</div>

<div id="content" class="col-lg-12">
  
  <!-- Manager Approvals Section -->
  <div class="box-inner" style="margin-bottom: 20px;">
    <div class="box-header well" style="background-color: #fcf8e3; color: #8a6d3b; border-color: #faebcc;">
      <h2 style="font-size: 13px;"><i class="glyphicon glyphicon-bell"></i> Mobile App Approvals</h2>
    </div>
    <div class="box-content row" style="padding: 20px;"> 
      <div class="col-md-12">
        <table class="table table-bordered table-striped" id="mobile_approvals_table">
          <thead>
            <tr>
              <th>Request Time</th>
              <th>User Name</th>
              <th>Zone / Rack</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="approvals_tbody">
            <tr><td colspan="5" class="text-center">Loading pending requests...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div><!--/#content.col-md-12-->

<hr>

<script>
$(document).ready(function() {
    // Polling for Manager Approvals
    function loadApprovals() {
        $.ajax({
            url: 'ajax_approvals.php',
            type: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            success: function(response) {
                let html = '';
                if (response.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No pending requests found.</td></tr>';
                } else {
                    response.forEach(function(req) {
                        html += `<tr>
                            <td>${req.REQUEST_TIME}</td>
                            <td>${req.USER_NAME}</td>
                            <td>${req.ZONE_NAME}</td>
                            <td><span class="label label-warning">${req.STATUS}</span></td>
                            <td>
                                <button type="button" class="btn btn-success btn-sm" onclick="approveRequest(${req.ID})">Approve</button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="rejectRequest(${req.ID})">Reject</button>
                            </td>
                        </tr>`;
                    });
                }
                $('#approvals_tbody').html(html);
            }
        });
    }

    window.approveRequest = function(id) {
        $.post('ajax_approvals.php', { action: 'approve', id: id }, function(res) {
            loadApprovals();
        });
    };

    window.rejectRequest = function(id) {
        $.post('ajax_approvals.php', { action: 'reject', id: id }, function(res) {
            loadApprovals();
        });
    };

    setInterval(loadApprovals, 5000);
    loadApprovals();
});
</script>

<?php require('footer.php'); ?>
