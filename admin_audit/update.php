<?php

ob_start();
?>
<?php require_once("includes/connection.php"); ?>
<?php

require_once("includes/functions.php");
include_once("includes/form_functions.php");

?>
<?php include('header.php'); ?><?php confirm_logged_in(); ?>
<?php

$user_name = $_GET['un'];

function Preview_manual_recounts() {
    global $conn;
    
    if (!isset($_POST['recounts']) || empty($_POST['recounts'])) {
        echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>No items were submitted.</div><a href='recounting.php' class='btn btn-primary'>Return</a></div>";
        return;
    }
    
    $pending_data = [];
    $has_error = false;
    
    foreach ($_POST['recounts'] as $row) {
        $item_code = trim($row['item_code']);
        $quanttity = trim($row['quantity']);
        $item_name = trim($row['item_name'] ?? 'UNKNOWN');
        
        // Double check against DB to be safe
        $real_item_query = "SELECT ITEM_CODE, ITEM_NAME FROM MASTER_ITEM WHERE ITEM_CODE = '{$item_code}' OR LTRIM(ITEM_CODE, '0') = LTRIM('{$item_code}', '0')";
        $real_stmt = oci_parse($conn, $real_item_query);
        oci_execute($real_stmt);
        $real_row = oci_fetch_array($real_stmt);
        
        $is_valid = false;
        
        if ($real_row && isset($real_row['ITEM_CODE'])) {
            $item_code = $real_row['ITEM_CODE'];
            $item_name = $real_row['ITEM_NAME'];
            $is_valid = true;
            
            // Calculate Existing Qty and Qty to Insert
            $s_code = $_SESSION['storecode'] ?? '';
            $audit_qty_query = "SELECT SUM(QTY) AS EXISTING_QTY FROM HEAD_AUDIT WHERE ITEM_CODE = '{$item_code}' AND SHOP_CODE = '{$s_code}' AND QTY <> 0";
            $audit_stmt = oci_parse($conn, $audit_qty_query);
            oci_execute($audit_stmt);
            $audit_row = oci_fetch_array($audit_stmt);
            $existing_qty = $audit_row['EXISTING_QTY'] ? $audit_row['EXISTING_QTY'] : 0;
            
            $qty_to_insert = $quanttity - $existing_qty;
            
        } else {
            $has_error = true;
            $existing_qty = 0;
            $qty_to_insert = 0;
        }
        
        $pending_data[] = [
            'item_code' => $item_code,
            'item_name' => $item_name,
            'quantity' => $quanttity,
            'existing_qty' => $existing_qty,
            'qty_to_insert' => $qty_to_insert,
            'is_valid' => $is_valid
        ];
    }
    
    $_SESSION['pending_recount_data'] = $pending_data;
    
    echo "<div class='container' style='margin-top:20px;'>";
    echo "<h2>Manual Recount Preview</h2>";
    
    if ($has_error) {
        echo "<div class='alert alert-danger'><strong>Error Detected!</strong> Some item codes are invalid.</div>";
    } else {
        echo "<div class='alert alert-success'>All items are valid! Please confirm to save.</div>";
    }
    
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Item Code</th><th>Item Name</th><th>Existing Scanned Qty</th><th>Recount Qty</th><th>Qty to Insert</th></tr></thead><tbody>";
    foreach ($pending_data as $row) {
        $color = $row['is_valid'] ? '#d4edda' : '#f8d7da';
        echo "<tr style='background-color: {$color};'>";
        echo "<td>" . htmlentities($row['item_code']) . "</td>";
        echo "<td>" . htmlentities($row['item_name']) . "</td>";
        echo "<td>" . htmlentities($row['existing_qty']) . "</td>";
        echo "<td>" . htmlentities($row['quantity']) . "</td>";
        echo "<td><strong>" . htmlentities($row['qty_to_insert']) . "</strong></td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    
    echo "<form method='post' action='update.php?un=" . urlencode($_GET['un'] ?? '') . "' style='display:inline-block; margin-right: 10px;'>";
    if ($has_error) {
        echo "<button type='button' class='btn btn-success' disabled>Confirm Save</button>";
    } else {
        echo "<button type='submit' name='confirm_manual_recounts' class='btn btn-success'>Confirm Save</button>";
    }
    echo "</form>";
    echo "<a href='recounting.php' class='btn btn-warning'>Cancel / Back</a>";
    echo "</div>";
}

function Confirm_manual_recounts() {
    global $conn;
    $user_name = $_SESSION['username'] ?? 'recount_manual';
    $s_code = $_SESSION['storecode'] ?? '';
    $staff_id = $_SESSION['staff_id'] ?? ''; 
    $dtime = date('Y-m-d H:i:s');
    $IP = $_SERVER["REMOTE_ADDR"];
    if ($IP == '::1') {
        $IP = '127.0.0.1';
    }

    if (!isset($_SESSION['pending_recount_data']) || empty($_SESSION['pending_recount_data'])) {
        echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>Session expired.</div><a href='recounting.php' class='btn btn-primary'>Return</a></div>";
        return;
    }

    $success_count = 0;
    $fail_count = 0;
    $audit_round = 2; // Recount is round 2?
    $recount_rack_num = 'Recount_U'; // The user specified specific rack

    foreach ($_SESSION['pending_recount_data'] as $row) {
        if (!$row['is_valid']) {
            $fail_count++;
            continue;
        }
        $item_code = $row['item_code'];
        $qty_to_insert = $row['qty_to_insert'];
        
        if ($qty_to_insert == 0) {
            $success_count++; // Skip insertion, but count as success
            continue;
        }

        $query = "INSERT INTO HEAD_AUDIT (
                        SHOP_CODE, ITEM_CODE, QTY, DATE_SYS, EMP_CODE, IP, USER_NAME, RACK_NUM, AUDIT_ROUND
                    ) VALUES (
                    '{$s_code}', '{$item_code}', '{$qty_to_insert}', '{$dtime}', '{$staff_id}', '$IP', '$user_name', '$recount_rack_num', {$audit_round}
                    )";
        
        $result = oci_parse($conn, $query);
        $exec_success = oci_execute($result, OCI_NO_AUTO_COMMIT);
        if ($exec_success) {
            $success_count++;
        } else {
            $fail_count++;
        }
    }
    oci_commit($conn);
    unset($_SESSION['pending_recount_data']);

    echo "<div class='container' style='margin-top:20px;'><div class='alert alert-success'>";
    echo "<h2>Recount Saved</h2>";
    echo "<p>Successfully saved $success_count items.</p>";
    if ($fail_count > 0) {
        echo "<p style='color:red;'>Failed to save $fail_count items.</p>";
    }
    echo "<a href=\"recounting.php\" class='btn btn-primary'>Return to Recounting</a>";
    echo "</div></div>";
}

function Preview_csv() {
    global $conn;
    $user_name = $_GET['un'] ?? '';
    
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");
        
        $item_idx = 0;
        $qty_idx = 1;
        $row_count = 0;
        
        $pending_data = [];
        $has_error = false;
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row_count++;
            
            if ($row_count == 1) {
                foreach ($data as $index => $header) {
                    $clean_header = trim(strtoupper(preg_replace('/[\x00-\x1F\x7F]/', '', $header)));
                    if (strpos($clean_header, 'ITEM_CODE') !== false) {
                        $item_idx = $index;
                    } elseif (strpos($clean_header, 'PHYSICAL_QUANTITY') !== false || strpos($clean_header, 'QTY') !== false) {
                        $qty_idx = $index;
                    }
                }
                continue; 
            }
            
            $item_code = isset($data[$item_idx]) ? trim($data[$item_idx]) : '';
            $quanttity = isset($data[$qty_idx]) ? trim($data[$qty_idx]) : '';
            
            $quanttity = preg_replace('/[^0-9.-]/', '', $quanttity);

            if (empty($item_code) || $quanttity === '' || !is_numeric($quanttity)) {
                continue;
            }

            // Verify with MASTER_ITEM
            $real_item_query = "SELECT ITEM_CODE, ITEM_NAME FROM MASTER_ITEM WHERE ITEM_CODE = '{$item_code}' OR LTRIM(ITEM_CODE, '0') = LTRIM('{$item_code}', '0')";
            $real_stmt = oci_parse($conn, $real_item_query);
            oci_execute($real_stmt);
            $real_row = oci_fetch_array($real_stmt);
            
            $is_valid = false;
            $item_name = 'UNKNOWN ITEM - TYPO DETECTED';
            
            if ($real_row && isset($real_row['ITEM_CODE'])) {
                $item_code = $real_row['ITEM_CODE'];
                $item_name = $real_row['ITEM_NAME'];
                $is_valid = true;
                
                // Calculate Existing Qty and Qty to Insert
                $s_code = $_SESSION['storecode'] ?? '';
                $audit_qty_query = "SELECT SUM(QTY) AS EXISTING_QTY FROM HEAD_AUDIT WHERE ITEM_CODE = '{$item_code}' AND SHOP_CODE = '{$s_code}' AND QTY <> 0";
                $audit_stmt = oci_parse($conn, $audit_qty_query);
                oci_execute($audit_stmt);
                $audit_row = oci_fetch_array($audit_stmt);
                $existing_qty = $audit_row['EXISTING_QTY'] ? $audit_row['EXISTING_QTY'] : 0;
                
                $qty_to_insert = $quanttity - $existing_qty;
                
            } else {
                $has_error = true;
                $existing_qty = 0;
                $qty_to_insert = 0;
            }
            
            $pending_data[] = [
                'item_code' => $item_code,
                'item_name' => $item_name,
                'quantity' => $quanttity,
                'existing_qty' => $existing_qty,
                'qty_to_insert' => $qty_to_insert,
                'is_valid' => $is_valid
            ];
        }
        fclose($handle);
        
        $_SESSION['pending_csv_data'] = $pending_data;
        
        echo "<div class='container' style='margin-top:20px;'>";
        echo "<h2>CSV Upload Preview</h2>";
        
        if ($has_error) {
            echo "<div class='alert alert-danger'><strong>Error Detected!</strong> Some item codes are incorrect. They are highlighted in red below. You cannot proceed until you fix your CSV file and re-upload.</div>";
        } else {
            echo "<div class='alert alert-success'>All item codes are valid! Please confirm to complete the upload.</div>";
        }
        
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Item Code</th><th>Item Name</th><th>Existing Scanned Qty</th><th>Recount Qty</th><th>Qty to Insert</th></tr></thead><tbody>";
        foreach ($pending_data as $row) {
            $color = $row['is_valid'] ? '#d4edda' : '#f8d7da';
            echo "<tr style='background-color: {$color};'>";
            echo "<td>" . htmlentities($row['item_code']) . "</td>";
            echo "<td>" . htmlentities($row['item_name']) . "</td>";
            echo "<td>" . htmlentities($row['existing_qty']) . "</td>";
            echo "<td>" . htmlentities($row['quantity']) . "</td>";
            echo "<td><strong>" . htmlentities($row['qty_to_insert']) . "</strong></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        
        echo "<form method='post' action='update.php?un=" . urlencode($_GET['un'] ?? '') . "' style='display:inline-block; margin-right: 10px;'>";
        if ($has_error) {
            echo "<button type='button' class='btn btn-success' onclick='alert(\"This item_code is wrong. Upload rejected. Please fix your CSV and re-upload.\")'>Confirm Upload</button>";
        } else {
            echo "<button type='submit' name='confirm_csv_upload' class='btn btn-success'>Confirm Upload</button>";
        }
        echo "</form>";
        echo "<a href='index.php' class='btn btn-warning'>Cancel / Back</a>";
        
        echo "</div>";
    } else {
        echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>";
        echo "<h2>File Upload Error</h2>";
        echo "<p>Please ensure a valid CSV file was selected.</p>";
        echo "<a href=\"index.php\" class='btn btn-primary'>Return to Main Page</a>";
        echo "</div></div>";
    }
}

function Confirm_csv() {
    global $conn;
    $user_name = $_GET['un'] ?? '';
    $s_code = $_SESSION['storecode'] ?? '';
    $staff_id = $_SESSION['staff_id'] ?? ''; 
    $dtime = date('Y-m-d H:i:s');
    $IP = $_SERVER["REMOTE_ADDR"];
    if ($IP == '::1') {
        $IP = '127.0.0.1';
    }

    if (!isset($_SESSION['pending_csv_data']) || empty($_SESSION['pending_csv_data'])) {
        echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>Session expired or no data to upload. Please try again.</div><a href='index.php' class='btn btn-primary'>Return</a></div>";
        return;
    }

    $success_count = 0;
    $fail_count = 0;
    $audit_round = 2;
    $csv_user_name = 'uploaded_excel';
    $csv_rack_num = 'Upload_U';

    foreach ($_SESSION['pending_csv_data'] as $row) {
        if (!$row['is_valid']) {
            $fail_count++;
            continue; // Safety fallback
        }
        $item_code = $row['item_code'];
        $qty_to_insert = $row['qty_to_insert'];
        
        if ($qty_to_insert == 0) {
            $success_count++; // Skip insertion, but count as success
            continue;
        }

        $query = "INSERT INTO HEAD_AUDIT (
                        SHOP_CODE, ITEM_CODE, QTY, DATE_SYS, EMP_CODE, IP, USER_NAME, RACK_NUM, AUDIT_ROUND
                    ) VALUES (
                    '{$s_code}', '{$item_code}', '{$qty_to_insert}', '{$dtime}', '{$staff_id}', '$IP', '$csv_user_name', '$csv_rack_num', {$audit_round}
                    )";
        
        $result = oci_parse($conn, $query);
        $exec_success = oci_execute($result, OCI_NO_AUTO_COMMIT);
        if ($exec_success) {
            $success_count++;
        } else {
            $fail_count++;
        }
    }
    oci_commit($conn);
    unset($_SESSION['pending_csv_data']); // Clear session

    echo "<div class='container' style='margin-top:20px;'><div class='alert alert-success'>";
    echo "<h2>CSV Upload Complete</h2>";
    echo "<p>Successfully uploaded $success_count items.</p>";
    if ($fail_count > 0) {
        echo "<p style='color:red;'>Failed to upload $fail_count items.</p>";
    }
    echo "<a href=\"index.php\" class='btn btn-primary'>Return to Main Page</a>";
    echo "</div></div>";
}

if (isset($_POST['upload_csv'])) {
    Preview_csv();
} else if (isset($_POST['confirm_csv_upload'])) {
    Confirm_csv();
} else if (isset($_POST['submit_manual_recounts'])) {
    Preview_manual_recounts();
} else if (isset($_POST['confirm_manual_recounts'])) {
    Confirm_manual_recounts();
} else if (isset($_POST['save'])) {
  
  $_SESSION['rack_number'] = $_POST['rack_number'];
  $rack_num = $_POST['rack_number'];
  $SHOP_CODE = $_SESSION['storecode'];
  
  // initialize an array to hold our errors
  $errors = array();

  //perform validation on the form data

  $fields_with_lengths = array('quanttity' => 10);
  $errors = array_merge($errors, check_max_field_lengths($fields_with_lengths));

  //clean up the form data before putting it in the database


  if (empty($errors)) {

    $item_code = $_POST['item_code'];
    $quanttity = $_POST['quanttity'];
    $s_code = $_POST['s_code'];
    $staff_id = $_POST['staff_id'];
    $dtime = date('Y-m-d H:i:s');
    $this_page = $_SERVER['PHP_SELF'];
    $IP = $_SERVER["REMOTE_ADDR"];
    if ($IP == '::1') {
        $IP = '127.0.0.1';
    }
    $date_auto = time();
    // Calculate the next AUDIT_ROUND for this specific item in this shop
    $round_query = "SELECT NVL(MAX(AUDIT_ROUND), 0) + 1 AS NEXT_ROUND FROM HEAD_AUDIT WHERE ITEM_CODE = '{$item_code}' AND SHOP_CODE = '{$s_code}'";
    $round_stmt = oci_parse($conn, $round_query);
    oci_execute($round_stmt);
    $round_row = oci_fetch_array($round_stmt);
    $audit_round = isset($round_row['NEXT_ROUND']) ? $round_row['NEXT_ROUND'] : 1;

    // // --- MANAGER REQUIREMENT: Archive and Truncate HEAD_AUDIT before new insert ---
    // $archive_stmt = oci_parse($conn, "INSERT INTO SHOP.HEAD_AUDIT_ARCHIVE SELECT * FROM SHOP.HEAD_AUDIT");
    // @oci_execute($archive_stmt);
    // @oci_free_statement($archive_stmt);
    
    // $truncate_stmt = oci_parse($conn, "TRUNCATE TABLE SHOP.HEAD_AUDIT");
    // @oci_execute($truncate_stmt);
    // @oci_free_statement($truncate_stmt);
    // -----------------------------------------------------------------------------

    $query = "INSERT INTO HEAD_AUDIT (
					SHOP_CODE, ITEM_CODE, QTY, DATE_SYS, EMP_CODE, IP, USER_NAME, RACK_NUM, AUDIT_ROUND
				)VALUES(
				'{$s_code}', '{$item_code}', '{$quanttity}', '{$dtime}', '{$staff_id}', '$IP', '$user_name', '$rack_num', {$audit_round}
				)";
    
    $result = oci_parse($conn, $query);
    $exec_success = oci_execute($result, OCI_NO_AUTO_COMMIT);
    
    if ($exec_success) {
      // Force an explicit commit
      $commit_success = oci_commit($conn);
      if ($commit_success) {
          redirect_to("index.php?d=1");
      } else {
          $e = oci_error($conn);
          echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>";
          echo "<h2>COMMIT FAILED</h2>";
          echo "<p>" . htmlentities($e['message'], ENT_QUOTES) . "</p>";
          echo "<a href=\"index.php\" class='btn btn-primary'>Return to Main Page</a>";
          echo "</div></div>";
      }
    } else {
      // Audit Failed
      $e = oci_error($result);
      echo "<div class='container' style='margin-top:20px;'><div class='alert alert-danger'>";
      echo "<h2>Product Audit failed.</h2>";
      echo "<p>" . htmlentities($e['message'], ENT_QUOTES) . "</p>";
      echo "<p><strong>Query:</strong> " . htmlentities($query) . "</p>";
      echo "<a href=\"index.php\" class='btn btn-primary'>Return to Main Page</a>";
      echo "</div></div>";
    }
  } else {
    if (count($errors) == 1) {
      $message = "<script> 

	 var call = confirm('There was 1 error in the form. Quantity can not accept more than 10 digits (including decimals)');  
	   if(call == false){
		   window.location.replace('index.php');
		   }
		   else{
			   window.location.replace('index.php');
			   }
	   </script>";
	echo $message;
    } else {
      // Errors occurred
      $message = "There were " . count($errors) . "errors in the form.";
      echo $message;
    }
  }
}
?>
<script>
// Ensure the global loader is hidden on this page so the user can see any error messages!
if (document.getElementById('global-page-loader')) {
    document.getElementById('global-page-loader').style.display = 'none';
}
</script>
<?php

ob_end_flush();
?>