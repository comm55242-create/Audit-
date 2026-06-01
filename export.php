<?php
// ==========================================================================
// AUDIT SETUP SYSTEM - PREMIUM STYLED EXCEL EXPORT ENGINE
// Streams a styled, colorized Excel spreadsheet representing the active scoping,
// placing summary metrics at the bottom and applying premium Melcom Green headers.
// ==========================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_data = isset($_POST['export_data']) ? $_POST['export_data'] : '';
    $stock_date = isset($_POST['stock_date']) ? $_POST['stock_date'] : date('Y-m-d');
    $shop_code = isset($_POST['shop_code']) ? strtoupper(trim($_POST['shop_code'])) : 'UNKNOWN';
    $audit_type = isset($_POST['audit_type']) ? $_POST['audit_type'] : '';
    $scanning_mode = isset($_POST['scanning_mode']) ? $_POST['scanning_mode'] : '';
    
    $total_items = isset($_POST['total_items']) ? $_POST['total_items'] : '0';
    $total_qty = isset($_POST['total_qty']) ? $_POST['total_qty'] : '0';
    $total_value = isset($_POST['total_value']) ? $_POST['total_value'] : 'GH₵ 0.00';

    $rows = [];
    if (!empty($raw_data)) {
        $rows = json_decode($raw_data, true);
    }

    // Sanitize file name representation and export as .xls for Excel HTML rendering
    $clean_shop = preg_replace('/[^A-Za-z0-9_\-]/', '', $shop_code);
    $filename = "audit_setup_" . $clean_shop . "_" . $stock_date . ".xls";

    // Stream as Microsoft Excel Spreadsheet with UTF-8 support
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Output HTML Excel structure with premium stylesheet definitions
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo '<style>';
    echo '  table { border-collapse: collapse; font-family: "Segoe UI", Arial, sans-serif; font-size: 10pt; }';
    echo '  th { background-color: #005028; color: #ffffff; font-weight: bold; border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; font-size: 11pt; }';
    echo '  td { border: 1px solid #e2e8f0; padding: 6px 12px; color: #334155; }';
    echo '  .summary-header { background-color: #005028; font-weight: bold; font-size: 12pt; text-transform: uppercase; color: #ffffff; height: 32px; }';
    echo '  .summary-label { background-color: #f8fafc; font-weight: bold; color: #475569; border: 1px solid #cbd5e1; }';
    echo '  .summary-value { font-weight: bold; color: #0f172a; border: 1px solid #cbd5e1; }';
    echo '  .metric-label { background-color: #f0fdf4; font-weight: bold; color: #166534; border: 1px solid #cbd5e1; }';
    echo '  .metric-value { background-color: #f0fdf4; font-weight: bold; color: #166534; font-size: 11pt; border: 1px solid #cbd5e1; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';

    echo '<table>';

    // 1. Write styled premium green table headers
    echo '<thead>';
    echo '<tr>';
    $headers = ['Item Code', 'Item Name', 'Barcode', 'Image', 'Price', 'Dept', 'Shop Code', 'Curr Stock', 'CH PI', 'CH Status', 'VC Group', 'VC Subgroup', 'VC Unit', 'VC Item Code', 'VC Shop Code', 'Stock Qyt'];
    foreach ($headers as $h) {
        echo '<th>' . htmlspecialchars($h) . '</th>';
    }
    echo '</tr>';
    echo '</thead>';

    // 2. Loop and write scoping rows
    echo '<tbody>';
    if (is_array($rows) && count($rows) > 0) {
        foreach ($rows as $row) {
            $item_code = isset($row['item_code']) ? $row['item_code'] : '';
            $item_name = isset($row['item_name']) ? $row['item_name'] : '';
            $barcode = isset($row['barcode']) ? $row['barcode'] : '';
            $image = isset($row['image']) ? $row['image'] : 'N/A';
            $price = isset($row['price']) ? $row['price'] : '';
            $dept = isset($row['dept']) ? $row['dept'] : '';
            $shop_code_val = isset($row['shop_code']) ? $row['shop_code'] : '';
            $curr_stock = isset($row['curr_stock']) ? $row['curr_stock'] : '0';
            $ch_pi = isset($row['ch_pi']) ? $row['ch_pi'] : 'N';
            $ch_status = isset($row['ch_status']) ? $row['ch_status'] : 'Y';
            $vc_group = isset($row['vc_group']) ? $row['vc_group'] : '';
            $vc_subgroup = isset($row['vc_subgroup']) ? $row['vc_subgroup'] : '';
            $vc_unit = isset($row['vc_unit']) ? $row['vc_unit'] : 'N/A';
            $vc_item_code = isset($row['vc_item_code']) ? $row['vc_item_code'] : '';
            $vc_shop_code = isset($row['vc_shop_code']) ? $row['vc_shop_code'] : '';
            $stock_qyt = isset($row['stock_qyt']) ? $row['stock_qyt'] : '0';

            echo '<tr>';
            // Force strict string representation to preserve leading zeros in Excel
            echo '<td style="mso-number-format:\'@\';">' . htmlspecialchars($item_code) . '</td>';
            echo '<td>' . htmlspecialchars($item_name) . '</td>';
            echo '<td style="mso-number-format:\'@\';">' . htmlspecialchars($barcode) . '</td>';
            echo '<td>' . htmlspecialchars($image) . '</td>';
            echo '<td>' . htmlspecialchars($price) . '</td>';
            echo '<td>' . htmlspecialchars($dept) . '</td>';
            echo '<td>' . htmlspecialchars($shop_code_val) . '</td>';
            echo '<td>' . htmlspecialchars($curr_stock) . '</td>';
            echo '<td>' . htmlspecialchars($ch_pi) . '</td>';
            echo '<td>' . htmlspecialchars($ch_status) . '</td>';
            echo '<td>' . htmlspecialchars($vc_group) . '</td>';
            echo '<td>' . htmlspecialchars($vc_subgroup) . '</td>';
            echo '<td>' . htmlspecialchars($vc_unit) . '</td>';
            echo '<td style="mso-number-format:\'@\';">' . htmlspecialchars($vc_item_code) . '</td>';
            echo '<td>' . htmlspecialchars($vc_shop_code) . '</td>';
            echo '<td>' . htmlspecialchars($stock_qyt) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="16" style="text-align: center;">No synced master items records found.</td></tr>';
    }
    echo '</tbody>';

    // 3. Spacers to push summary card to the bottom
    echo '<tr><td colspan="16" style="border: none; height: 24px;"></td></tr>';
    echo '<tr><td colspan="16" style="border: none; height: 24px;"></td></tr>';

    // 4. Premium Styled Summary Card at the BOTTOM of the spreadsheet
    echo '<tr>';
    echo '<td colspan="16" class="summary-header" style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">MELCOM AUDIT SYSTEM - STOCK TAKE SCOPING SUMMARY</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="4" class="summary-label">Shop Code</td>';
    echo '<td colspan="12" class="summary-value">' . htmlspecialchars($shop_code) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="4" class="summary-label">Stock Date</td>';
    echo '<td colspan="12" class="summary-value">' . htmlspecialchars($stock_date) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="4" class="summary-label">Audit Type</td>';
    echo '<td colspan="12" class="summary-value">' . htmlspecialchars($audit_type) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="4" class="summary-label">Scanning Mode</td>';
    echo '<td colspan="12" class="summary-value">' . htmlspecialchars($scanning_mode) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="4" class="metric-label">Total Items</td>';
    echo '<td colspan="12" class="metric-value">' . htmlspecialchars($total_items) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="4" class="metric-label">Total Qty</td>';
    echo '<td colspan="12" class="metric-value">' . htmlspecialchars($total_qty) . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td colspan="4" class="metric-label">Total Value</td>';
    echo '<td colspan="12" class="metric-value">' . htmlspecialchars($total_value) . '</td>';
    echo '</tr>';

    echo '</table>';
    echo '</body>';
    echo '</html>';
    exit;
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo "Invalid Request Method.";
}
?>
