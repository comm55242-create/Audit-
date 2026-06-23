<?php
require_once("includes/connection.php");
?>
<?php if (!isset($no_visible_elements) || !$no_visible_elements) { ?>
  <!-- content ends -->
  </div><!--/#content.col-md-0-->
<?php } ?>
</div><!--/fluid-row-->
<?php if (!isset($no_visible_elements) || !$no_visible_elements) { ?>

  <!-- Ad, you can remove it -->
  <!-- Ad ends -->

  <hr><div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
           aria-hidden="true">

    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h3>Details</h3>
        </div>
        <div class="modal-body">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Profile</a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Reports</a></li>
            <?php
            $rita3 = $_SESSION['storecode'];
            $rita = $_SESSION['staff_id'];
//$oot = $_SESSION['user_id'];
            $add = oci_parse($conn, "SELECT ADMIN FROM USERS WHERE STAFF_ID = '{$rita}'");
            oci_execute($add);
            $ytu = oci_fetch_array($add);

            if ($ytu['ADMIN'] == '1') {
              ?>
              <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Staffs Report</a></li>
              <li role="presentation"><a href="#messages2" aria-controls="messages" role="tab" data-toggle="tab">Department Report</a></li>
              
              <li role="presentation" class="dropdown"> 
                <a href="#" class="dropdown-toggle" id="myTabDrop1" data-toggle="dropdown" aria-controls="myTabDrop1-contents" aria-expanded="false">New Reports <span class="caret"></span></a> 
                <ul class="dropdown-menu" aria-labelledby="myTabDrop1" id="myTabDrop1-contents"> 
                  <li>
                    <a href="#messages23" aria-controls="messages" role="tab" data-toggle="tab">All Reports</a>
                  </li> 
                  <li>
                    <a href="#dropdown1" role="tab" id="dropdown1-tab" data-toggle="tab" aria-controls="dropdown1" aria-expanded="true">Audit Report</a>
                  </li> 
                  <li>
                    <a href="#dropdown2" role="tab" id="dropdown2-tab" data-toggle="tab" aria-controls="dropdown2">Pending stock take</a>
                  </li>
                  <li>
                    <a href="#dropdown3" role="tab" id="dropdown2-tab" data-toggle="tab" aria-controls="dropdown3">Discrepancy Report</a>
                  </li>
                  <li><a href="#messages24" aria-controls="messages" role="tab" data-toggle="tab"><?php echo $rita3; ?> Stock Audit Report ERP</a></li>
                </ul> 
              </li>

            <?php } else { ?>
  <?php } ?>
          </ul>
          <!-- Tab panes -->
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home"><?php
              $sql2 = oci_parse($conn, "SELECT E_NAME, ADMIN FROM USERS WHERE STAFF_ID = '{$rita}'");
              oci_execute($sql2);
              $ben = oci_fetch_array($sql2);
              $e_name2 = $ben['E_NAME'];
              $status = $ben['ADMIN'];
              ?>
              <p><strong>Name: </strong> <?php echo $e_name2; ?></p><p><strong>Staff Id: </strong> <?php echo $rita; ?></p>
              <p><strong>Status: </strong> <?php if ($ben['ADMIN'] == 1) { ?>
                  Administrator
                <?php } else { ?>
                  User
                <?php } ?></p></div> 
            <div role="tabpanel" class="tab-pane" id="profile"><a href="report.php" title="View Report For Today="left"." data-toggle="tooltip" class="btn btn-warning">View Report</a></div>
            <div role="tabpanel" class="tab-pane" id="messages"><?php
//$oot = $_SESSION['user_id'];
              $add = oci_parse($conn, "SELECT ADMIN FROM USERS WHERE STAFF_ID = '{$rita}'");
              oci_execute($add);
              $ytu = oci_fetch_array($add);

              if ($ytu['ADMIN'] == 1) {
                $add2 = oci_parse($conn, "SELECT STAFF_ID, E_NAME FROM USERS WHERE ADMIN = '0'");
                oci_execute($add2);
                while ($ytu2 = oci_fetch_array($add2)) {
                  $ttru = $ytu2['STAFF_ID'];
                  if (isset($ttru)) {
                    $sql = oci_parse($conn, "SELECT * FROM HEAD_AUDIT WHERE EMP_CODE = '{$ttru}'");
                    oci_execute($sql);
                    $trot = oci_fetch_all($sql, $res);
                    ?>
                    <div class="list-group">
                      <a href="adrep.php?tanto=<?php echo $ttru; ?>" class="list-group-item ">
                        <h4 class="list-group-item-heading"><?php echo $ytu2['E_NAME']; ?></h4>
                        <p class="list-group-item-heading"><?php echo $trot; ?> Audit entries have been made</p> 
                      </a>

                    </div>

                  <?php }
                } ?>


              <?php } else { ?>
  <?php } ?></div>

            <div role="tabpanel" class="tab-pane" id="messages2"><?php
              $trobil = oci_parse($conn, "SELECT DISTINCT DEPT FROM MASTER_ITEM ORDER BY DEPT DESC");
              oci_execute($trobil);
              while ($tifa = oci_fetch_array($trobil)) {
                $dept = str_replace("&", "%26", str_replace(" ", "-", $tifa['DEPT']));
                ?>

                <div class="list-group">
                  <a href="adrep2.php?tanto=<?php echo $dept; ?>" class="list-group-item ">
                    <h4 class="list-group-item-heading"><?php echo str_replace("-", " ", str_replace("%26", "&", $dept)); ?></h4>
                    <noscript> <p class="list-group-item-heading"> Audit entries have been made</p> </noscript>
                  </a>   
                </div><?php } ?>

            </div>
            <div role="tabpanel" class="tab-pane" id="messages23"><a href="adrepall.php?addon=default" title="View All Report For Today" data-toggle="tooltip" class="btn btn-warning">View Reports</a></div>
            <div role="tabpanel" class="tab-pane" id="dropdown1"><a href="adrepall.php?addon=yes" title="View All Report For Today" data-toggle="tooltip" class="btn btn-warning">View Audit Report</a></div>
            <div role="tabpanel" class="tab-pane" id="dropdown2">
                <?php
                $sql = oci_parse($conn, "SELECT DISTINCT DEPT FROM ZS_VW_AUDIT_PENDING");
                if (!$sql) {
                  $e2 = oci_error($conn);
                  trigger_error(htmlentities($e2['message'], ENT_QUOTES), E_USER_ERROR);
                }
                $r2 = oci_execute($sql);
                ?>
              
              <form method="POST" action="adrepall.php">
                <div class="form-group">
                  <label for="inputSelectDepartment">Select Department</label>
                  <select name="pending_department" class="form-control">
                    <option>All</option>                    
                    <?php while ($row = oci_fetch_array($sql)): ?>
                      <option><?php echo $row['DEPT']; ?></option>
                    <?php endwhile; ?>
                  </select>
                </div>
                <input name="pending_stock" type="submit" value="View pending stock take" class="btn btn-primary">
              </form>
            </div>
            <div role="tabpanel" class="tab-pane" id="dropdown3"><a href="adrepall.php?addon=discrepancy" title="View Discrepancy Report" data-toggle="tooltip" class="btn btn-warning">View Discrepancy Report</a></div>


            <div role="tabpanel" class="tab-pane" id="messages24"><a href="adrepstr.php" title="View All Report For Today" data-toggle="tooltip" class="btn btn-warning">View Reports</a></div>


          </div>

        </div>


        <div class="modal-footer">
          <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>

        </div>
      </div>
    </div>
  </div>
  <footer class="row">
    <p class="col-md-9 col-sm-9 col-xs-12 copyright">&copy; <a href="http://usman.it" target="_blank">Melcom Group Of Companies</a> 2016 - <?php echo date('Y') ?></p>

    <p class="col-md-3 col-sm-3 col-xs-12 powered-by">Developed by : <a
        href="http://usman.it/free-responsive-admin-template">Melcom IT Team</a></p>
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
//Google Analytics code for tracking my demo site, you can remove this.
if ($_SERVER['HTTP_HOST'] == 'usman.it') {
  ?>
  <script>
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-26532312-1']);
    _gaq.push(['_trackPageview']);
    (function () {
      var ga = document.createElement('script');
      ga.type = 'text/javascript';
      ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
    })();
  </script>
<?php } ?>


</body>
</html>
