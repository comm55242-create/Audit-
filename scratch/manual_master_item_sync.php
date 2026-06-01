<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - STANDALONE MASTER_ITEM MANUAL SYNC & QUERY BUILDER
// Location: c:\Users\USER\Workspaces\htdocs\Melcom Mobile\scratch\manual_master_item_sync.php
// Use this script on the audit laptop or server where connection is active.
// ==========================================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// --------------------------------------------------------------------------
// SECTION 1: RAW SQL QUERIES FOR DIRECT DATABASE TOOLS (Toad, SQLDeveloper, PL/SQL Developer)
// --------------------------------------------------------------------------
/*
-- A. TRUNCATE THE LOCAL MASTER_ITEM TABLE
TRUNCATE TABLE SHOP.MASTER_ITEM;

-- B. INSERT CORRESPONDING ITEMS FROM REMOTE VS_ITEM_AUDIT VIEW BASED ON SELECTED DEPT/GROUP/SUBGROUP NAMES (Joining POS.SHOP_STOCK_SUMMARY for CURR_STOCK)
INSERT INTO SHOP.MASTER_ITEM (
    ITEM_CODE, 
    ITEM_NAME, 
    BARCODE, 
    IMAGE,
    PRICE, 
    DEPT, 
    SHOP_CODE, 
    CURR_STOCK,
    CH_PI, 
    CH_STATUS,
    VC_GROUP, 
    VC_SUBGROUP,
    VC_UNIT,
    VC_ITEM_CODE,
    VC_SHOP_CODE,
    NU_BALANCE_QTY
)
SELECT DISTINCT 
    TRIM(A.ITEM_CODE), 
    SUBSTR(TRIM(A.ITEM_NAME), 1, 100), 
    TRIM(A.BARCODE), 
    NULL AS IMAGE,
    A.PRICE, 
    SUBSTR(TRIM(A.DEPT_CODE), 1, 50), 
    NVL(TRIM(S.VC_SHOP_CODE), 'SC001'), 
    NVL(S.NU_BALANCE_QTY, 0), 
    NVL(A.CH_PI, 'N'), 
    NVL(A.CH_STATUS, 'Y'),
    SUBSTR(TRIM(D.VC_GROUP_CODE), 1, 50), 
    SUBSTR(TRIM(D.VC_SUB_GROUP_CODE), 1, 50),
    SUBSTR(TRIM(A.VC_UNIT), 1, 12),
    NVL(TRIM(S.VC_ITEM_CODE), A.ITEM_CODE),
    NVL(TRIM(S.VC_SHOP_CODE), 'SC001'),
    NVL(S.NU_BALANCE_QTY, 0)
FROM VS_ITEM_AUDIT@DB_LINK_SHOP A
LEFT OUTER JOIN VW_STK_DEPT@DB_LINK_SHOP D
  ON D.DEPT_CODE = A.DEPT_CODE 
 AND D.GROUPS = A.GROUPS 
 AND D.SUB_GROUP = A.SUB_GROUP
LEFT OUTER JOIN POS.SHOP_STOCK_SUMMARY@DB_LINK_SHOP S
  ON S.VC_ITEM_CODE = A.ITEM_CODE
 AND S.VC_COMP_CODE = '01'
 AND S.VC_SHOP_CODE = 'SC001'
WHERE A.ITEM_CODE IS NOT NULL
  AND TRIM(D.DEPT_CODE) IN ('SUPERMARKET') -- Selected department codes
  AND TRIM(D.VC_GROUP_CODE) IN ('PROVISIONS', 'TOILETRIES') -- Selected group codes
  AND TRIM(D.VC_SUB_GROUP_CODE) IN ('MILK', 'SOAP'); -- Selected subgroup codes

COMMIT;
*/

// --------------------------------------------------------------------------
// SECTION 2: STANDALONE PHP UTILITY CODE (Run this directly via CLI or Browser)
// --------------------------------------------------------------------------
echo "==========================================================================\n";
echo "MELCOM STOCK SETUP: LOCAL MASTER_ITEM SYNCHRONIZATION TOOL\n";
echo "==========================================================================\n\n";

// Local database credentials
$db_host = "localhost";
$db_port = "1521";
$db_service = "orcl"; // or "xe"
$db_user = "SHOP";
$db_pass = "SHOP";

$tns = "(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = $db_host)(PORT = $db_port))
    (CONNECT_DATA =
      (SERVER = DEDICATED)
      (SERVICE_NAME = $db_service)
    )
  )";

echo "[-] Connecting to local database at $db_host:$db_port/$db_service...\n";
$conn = @oci_connect($db_user, $db_pass, $tns);

if (!$conn) {
    $e = oci_error();
    echo "[!] Connection failed: " . $e['message'] . "\n";
    echo "[!] Ensure this script is run on the AUDIT LAPTOP or SERVER with connection privileges.\n";
    exit(1);
}
echo "[+] Connected successfully to local schema!\n\n";

// 1. Define Scope filters and Shop parameters
$shop_code = "SC001"; // Active Shop Code
$selected_departments = ['SUPERMARKET'];
$selected_groups = ['PROVISIONS', 'TOILETRIES'];
$selected_subgroups = ['MILK', 'SOAP'];
$audit_type = "SST"; // "PI" (Complete catalog) or "SST" (Filtered)

echo "[-] Target Scope Filter Configured:\n";
echo "    Shop Code   : " . $shop_code . "\n";
echo "    Type        : " . $audit_type . "\n";
echo "    Departments : " . implode(', ', $selected_departments) . "\n";
echo "    Groups      : " . implode(', ', $selected_groups) . "\n";
echo "    Subgroups   : " . implode(', ', $selected_subgroups) . "\n\n";

// 2. Clear/Truncate local table
echo "[-] Truncating table MASTER_ITEM...\n";
$trunc_stmt = @oci_parse($conn, "TRUNCATE TABLE SHOP.MASTER_ITEM");
$trunc_ok = @oci_execute($trunc_stmt);
if ($trunc_stmt) oci_free_statement($trunc_stmt);

if (!$trunc_ok) {
    echo "[-] Truncate failed (privilege restriction). Falling back to DELETE...\n";
    $del_stmt = oci_parse($conn, "DELETE FROM SHOP.MASTER_ITEM");
    oci_execute($del_stmt);
    if ($del_stmt) oci_free_statement($del_stmt);
}
echo "[+] MASTER_ITEM cleared successfully.\n\n";

// 3. Build dynamic insert query
echo "[-] Building custom INSERT SELECT statement...\n";

$select_query = "SELECT DISTINCT 
                    TRIM(A.ITEM_CODE) AS ITEM_CODE, 
                    SUBSTR(TRIM(A.ITEM_NAME), 1, 100) AS ITEM_NAME, 
                    TRIM(A.BARCODE) AS BARCODE, 
                    NULL AS IMAGE, 
                    A.PRICE, 
                    SUBSTR(TRIM(A.DEPT_CODE), 1, 50) AS DEPT, 
                    NVL(TRIM(S.VC_SHOP_CODE), :shop_code) AS SHOP_CODE, 
                    NVL(S.NU_BALANCE_QTY, 0) AS CURR_STOCK, 
                    NVL(A.CH_PI, 'N') AS CH_PI, 
                    NVL(A.CH_STATUS, 'Y') AS CH_STATUS, 
                    SUBSTR(TRIM(D.VC_GROUP_CODE), 1, 50) AS VC_GROUP, 
                    SUBSTR(TRIM(D.VC_SUB_GROUP_CODE), 1, 50) AS VC_SUBGROUP, 
                    SUBSTR(TRIM(A.VC_UNIT), 1, 12) AS VC_UNIT,
                    NVL(TRIM(S.VC_ITEM_CODE), A.ITEM_CODE) AS VC_ITEM_CODE,
                    NVL(TRIM(S.VC_SHOP_CODE), :shop_code) AS VC_SHOP_CODE,
                    NVL(S.NU_BALANCE_QTY, 0) AS STOCK_QYT 
                 FROM VS_ITEM_AUDIT@DB_LINK_SHOP A
                 LEFT OUTER JOIN VW_STK_DEPT@DB_LINK_SHOP D
                   ON D.DEPT_CODE = A.DEPT_CODE 
                  AND D.GROUPS = A.GROUPS 
                  AND D.SUB_GROUP = A.SUB_GROUP
                 LEFT OUTER JOIN POS.SHOP_STOCK_SUMMARY@DB_LINK_SHOP S
                   ON S.VC_ITEM_CODE = A.ITEM_CODE
                  AND S.VC_COMP_CODE = '01'
                  AND S.VC_SHOP_CODE = :shop_code";

$where_clauses = ["A.ITEM_CODE IS NOT NULL"];
$binds = [':shop_code' => substr(strtoupper(trim($shop_code)), 0, 10)];

if ($audit_type !== 'PI') {
    if (!empty($selected_departments)) {
        $phs = [];
        for ($i = 0; $i < count($selected_departments); $i++) {
            $phs[] = ':dept' . $i;
            $binds[':dept' . $i] = $selected_departments[$i];
        }
        $where_clauses[] = "TRIM(D.DEPT_CODE) IN (" . implode(', ', $phs) . ")";
    }

    if (!empty($selected_groups)) {
        $phs = [];
        for ($i = 0; $i < count($selected_groups); $i++) {
            $phs[] = ':grp' . $i;
            $binds[':grp' . $i] = $selected_groups[$i];
        }
        $where_clauses[] = "TRIM(D.VC_GROUP_CODE) IN (" . implode(', ', $phs) . ")";
    }

    if (!empty($selected_subgroups)) {
        $phs = [];
        for ($i = 0; $i < count($selected_subgroups); $i++) {
            $phs[] = ':sub' . $i;
            $binds[':sub' . $i] = $selected_subgroups[$i];
        }
        $where_clauses[] = "TRIM(D.VC_SUB_GROUP_CODE) IN (" . implode(', ', $phs) . ")";
    }
}

if (!empty($where_clauses)) {
    $select_query .= " WHERE " . implode(' AND ', $where_clauses);
}

$final_query = "INSERT INTO SHOP.MASTER_ITEM (
                    ITEM_CODE, ITEM_NAME, BARCODE, IMAGE, PRICE, DEPT, SHOP_CODE, CURR_STOCK, CH_PI, CH_STATUS, VC_GROUP, VC_SUBGROUP, VC_UNIT, VC_ITEM_CODE, VC_SHOP_CODE, STOCK_QYT
                ) $select_query";

echo "[-] EXECUTING QUERY:\n$final_query\n\n";

$stmt = oci_parse($conn, $final_query);
foreach ($binds as $placeholder => $val) {
    oci_bind_by_name($stmt, $placeholder, $binds[$placeholder]);
}

$exec = @oci_execute($stmt);
if ($exec) {
    $rows_inserted = oci_num_rows($stmt);
    echo "[+] Successful! Inserted $rows_inserted matching item records into MASTER_ITEM!\n";
} else {
    $e = oci_error($stmt);
    echo "[!] SQL execution error: " . $e['message'] . "\n";
}
if ($stmt) oci_free_statement($stmt);

// Commit connection
$commit_stmt = oci_parse($conn, "COMMIT");
oci_execute($commit_stmt);
oci_free_statement($commit_stmt);

echo "\n[-] Closing active Oracle connection...\n";
oci_close($conn);
echo "[+] Completed successfully!\n";
echo "==========================================================================\n";
?>
