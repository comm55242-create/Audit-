<?php
// Force display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// We will try connecting using the main Database class
require_once __DIR__ . '/../models/Database.php';

try {
    $conn = Database::getConnection();
    if ($conn) {
        $output = "COLUMNS OF SHOP.MASTER_ITEM:\n";
        $query = "SELECT column_name, data_type, data_length, nullable 
                  FROM user_tab_cols 
                  WHERE table_name = 'MASTER_ITEM' 
                  ORDER BY column_id";
        $stmt = oci_parse($conn, $query);
        if (oci_execute($stmt)) {
            while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                $output .= sprintf("%-20s %-15s %-10d %-5s\n", 
                    $row['COLUMN_NAME'], 
                    $row['DATA_TYPE'], 
                    $row['DATA_LENGTH'], 
                    $row['NULLABLE']
                );
            }
        }
        oci_free_statement($stmt);
        file_put_contents(__DIR__ . '/master_item_cols.txt', $output);
        echo "Successfully wrote master item columns to scratch/master_item_cols.txt!\n";
    }
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/master_item_cols.txt', "Error: " . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}
?>
