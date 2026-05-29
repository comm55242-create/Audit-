<?php
// ==========================================================================
// AUDIT SETUP SYSTEM - SECURE CSV EXPORT ENGINE
// Streams a relational, clean comma-separated values (CSV) file representing
// all active rows in the configuration summary table.
// ==========================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_data = isset($_POST['export_data']) ? $_POST['export_data'] : '';
    $stock_date = isset($_POST['stock_date']) ? $_POST['stock_date'] : date('Y-m-d');
    $shop_code = isset($_POST['shop_code']) ? strtoupper(trim($_POST['shop_code'])) : 'UNKNOWN';
    $audit_type = isset($_POST['audit_type']) ? $_POST['audit_type'] : '';
    $scanning_mode = isset($_POST['scanning_mode']) ? $_POST['scanning_mode'] : '';

    $rows = [];
    if (!empty($raw_data)) {
        $rows = json_decode($raw_data, true);
    }

    // Sanitize file name representation
    $clean_shop = preg_replace('/[^A-Za-z0-9_\-]/', '', $shop_code);
    $filename = "audit_setup_" . $clean_shop . "_" . $stock_date . ".csv";

    // Set standard browser output headers to prompt file save/download dialog
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open dynamic write output buffer stream
    $output = fopen('php://output', 'w');

    // Write column headers
    fputcsv($output, ['Stock Date', 'Shop Code', 'Audit Type', 'Mode', 'Department', 'Group', 'Subgroup']);

    // Loop and write row coordinates
    if (is_array($rows) && count($rows) > 0) {
        foreach ($rows as $row) {
            // Relational Autofill: fill empty visual spacer cells with main metadata values 
            // to make the exported CSV 100% useful for automated imports / database parsing.
            $row_date = !empty($row['stock_date']) ? $row['stock_date'] : $stock_date;
            $row_shop = !empty($row['shop_code']) ? $row['shop_code'] : $shop_code;
            
            // Clean up any HTML tag wrappers (like badge spans)
            $row_type = !empty($row['audit_type']) ? strip_tags($row['audit_type']) : strip_tags($audit_type);
            $row_mode = !empty($row['scanning_mode']) ? strip_tags($row['scanning_mode']) : strip_tags($scanning_mode);
            
            $dept = isset($row['department']) ? $row['department'] : '';
            $group = isset($row['group']) ? $row['group'] : '';
            $subgroup = isset($row['subgroup']) ? $row['subgroup'] : '';

            fputcsv($output, [$row_date, $row_shop, $row_type, $row_mode, $dept, $group, $subgroup]);
        }
    } else {
        // Fallback default
        fputcsv($output, [$stock_date, $shop_code, strip_tags($audit_type), strip_tags($scanning_mode), 'ALL DEPARTMENTS', 'ALL GROUPS', 'ALL SUBGROUPS']);
    }

    fclose($output);
    exit;
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Invalid Request Method.";
}
?>
