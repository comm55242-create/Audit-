<?php
require_once('Audit_new/admin_audit/includes/connection.php');

$query = "
CREATE TABLE RECOUNT_APPROVALS (
    ID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    SHOP_CODE VARCHAR2(50) NOT NULL,
    USER_NAME VARCHAR2(100) NOT NULL,
    ZONE_NAME VARCHAR2(100) NOT NULL,
    STATUS VARCHAR2(20) DEFAULT 'PENDING',
    REQUEST_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
";

$stmt = oci_parse($conn, $query);
$success = oci_execute($stmt);

if ($success) {
    echo "Table created successfully.";
} else {
    $e = oci_error($stmt);
    echo "Error: " . $e['message'];
}

oci_free_statement($stmt);
oci_close($conn);
?>
