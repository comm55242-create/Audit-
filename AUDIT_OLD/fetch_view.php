<?php
require_once("includes/connection.php");
$query = "SELECT TEXT FROM ALL_VIEWS WHERE VIEW_NAME = 'ZS_VW_AUDIT_REPORT'";
$stmt = oci_parse($conn, $query);
oci_execute($stmt);
while ($row = oci_fetch_assoc($stmt)) {
    echo $row['TEXT'];
}
?>
