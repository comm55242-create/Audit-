<?php
require_once __DIR__ . '/../models/Database.php';

try {
    $conn = Database::getConnection();
    if (!$conn) {
        die("Connection failed");
    }
    
    echo "COLUMNS OF SHOP.MASTER_ITEM:\n";
    $query = "SELECT column_name, data_type, data_length, nullable 
              FROM user_tab_cols 
              WHERE table_name = 'MASTER_ITEM' 
              ORDER BY column_id";
    $stmt = oci_parse($conn, $query);
    if (oci_execute($stmt)) {
        while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
            printf("%-20s %-15s %-10d %-5s\n", 
                $row['COLUMN_NAME'], 
                $row['DATA_TYPE'], 
                $row['DATA_LENGTH'], 
                $row['NULLABLE']
            );
        }
    }
    oci_free_statement($stmt);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
