<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../models/Database.php';

try {
    $conn = Database::getConnection();
    
    // Check AUDIT_SETUP latest entry
    $q1 = "SELECT * FROM SHOP.AUDIT_SETUP ORDER BY SETUP_ID DESC FETCH FIRST 1 ROWS ONLY";
    $stmt1 = oci_parse($conn, $q1);
    oci_execute($stmt1);
    $setup = oci_fetch_array($stmt1, OCI_ASSOC);
    
    echo "LATEST AUDIT_SETUP:\n";
    if ($setup) {
        $depts = $setup['SELECTED_DEPTS'] !== null ? $setup['SELECTED_DEPTS']->load() : 'NULL';
        $groups = $setup['SELECTED_GROUPS'] !== null ? $setup['SELECTED_GROUPS']->load() : 'NULL';
        $subgroups = $setup['SELECTED_SUBGROUPS'] !== null ? $setup['SELECTED_SUBGROUPS']->load() : 'NULL';
        echo "SETUP_ID: " . $setup['SETUP_ID'] . "\n";
        echo "DEPTS: " . $depts . "\n";
        echo "GROUPS: " . $groups . "\n";
        echo "SUBGROUPS: " . $subgroups . "\n";
    } else {
        echo "No entries found.\n";
    }
    oci_free_statement($stmt1);

    // Check MASTER_ITEM breakdown
    $q2 = "SELECT DEPT_CODE, COUNT(*) AS NUM_ITEMS FROM SHOP.MASTER_ITEM GROUP BY DEPT_CODE";
    $stmt2 = oci_parse($conn, $q2);
    oci_execute($stmt2);
    
    echo "\nMASTER_ITEM BREAKDOWN BY DEPT_CODE:\n";
    while ($row = oci_fetch_array($stmt2, OCI_ASSOC)) {
        echo "DEPT_CODE: " . $row['DEPT_CODE'] . " -> " . $row['NUM_ITEMS'] . " items\n";
    }
    oci_free_statement($stmt2);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
