<?php
// Forces the browser to download a CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="audit_upload_format.csv"');

// Open the output stream
$output = fopen('php://output', 'w');

// Output the exact required headers
fputcsv($output, array('ITEM_CODE', 'PHYSICAL_QUANTITY'));

fclose($output);
exit();
?>
