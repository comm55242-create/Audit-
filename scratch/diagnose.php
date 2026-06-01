<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../models/Database.php';

try {
    $conn = Database::getConnection();
    echo "Connected successfully to database on audit laptop!\n";
    
    echo "Describing remote view VS_ITEM_AUDIT@DB_LINK_SHOP...\n";
    $query = "SELECT * FROM VS_ITEM_AUDIT@DB_LINK_SHOP WHERE ROWNUM = 1";
    $stmt = oci_parse($conn, $query);
    
    if ($stmt && @oci_execute($stmt)) {
        $ncols = oci_num_fields($stmt);
        $output = "Columns of VS_ITEM_AUDIT@DB_LINK_SHOP:\n";
        for ($i = 1; $i <= $ncols; $i++) {
            $column_name  = oci_field_name($stmt, $i);
            $column_type  = oci_field_type($stmt, $i);
            $column_size  = oci_field_size($stmt, $i);
            $output .= "$column_name ($column_type, size $column_size)\n";
        }
        
        file_put_contents(__DIR__ . '/remote_cols.txt', $output);
        echo "Successfully wrote remote columns list to scratch/remote_cols.txt!\n";
        oci_free_statement($stmt);
    } else {
        $e = oci_error($stmt);
        echo "Error querying remote view: " . $e['message'] . "\n";
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
