<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - AUDIT MODEL
// Encapsulates all Oracle queries, dynamic lookups, and setup parameter saves.
// ==========================================================================

require_once 'Database.php';

class AuditModel {

    /**
     * Executes real-time search queries against MASTER_ITEM.
     */
    public static function searchItems($query) {
        $conn = Database::getConnection();
        $query_param = strtoupper(trim($query));
        if (empty($query_param)) {
            return [];
        }

        $search_like = '%' . $query_param . '%';
        $sql = "SELECT * FROM (
                    SELECT 
                        TRIM(ITEM_CODE) as ITEM_CODE, 
                        TRIM(ITEM_NAME) as ITEM_NAME, 
                        TRIM(BARCODE) as BARCODE, 
                        PRICE, 
                        CURR_STOCK, 
                        TRIM(DEPT) as DEPT, 
                        TRIM(VC_GROUP) as VC_GROUP, 
                        TRIM(VC_SUBGROUP) as VC_SUBGROUP 
                    FROM MASTER_ITEM 
                    WHERE UPPER(ITEM_CODE) LIKE :query 
                       OR UPPER(ITEM_NAME) LIKE :query 
                       OR UPPER(BARCODE) LIKE :query
                    ORDER BY ITEM_CODE
                ) WHERE ROWNUM <= 50";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':query', $search_like);

        $results = [];
        if ($stmt && @oci_execute($stmt)) {
            while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                $results[] = [
                    'ITEM_CODE' => isset($row['ITEM_CODE']) ? $row['ITEM_CODE'] : 'N/A',
                    'ITEM_NAME' => isset($row['ITEM_NAME']) ? $row['ITEM_NAME'] : 'UNNAMED',
                    'BARCODE' => isset($row['BARCODE']) ? $row['BARCODE'] : 'N/A',
                    'PRICE' => $row['PRICE'] !== null ? floatval($row['PRICE']) : 0.00,
                    'CURR_STOCK' => $row['CURR_STOCK'] !== null ? intval($row['CURR_STOCK']) : 0,
                    'DEPT' => isset($row['DEPT']) ? $row['DEPT'] : 'GENERAL',
                    'VC_GROUP' => isset($row['VC_GROUP']) ? $row['VC_GROUP'] : 'GENERAL',
                    'VC_SUBGROUP' => isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : 'GENERAL'
                ];
            }
        }
        if ($stmt) oci_free_statement($stmt);
        return $results;
    }

    /**
     * Executes rapid parameter-bound batch lookups for CSV item configurations.
     */
    public static function lookupBatchItems($codes) {
        $conn = Database::getConnection();
        if (empty($codes) || !is_array($codes)) {
            return [];
        }

        $clean_codes = [];
        foreach ($codes as $c) {
            $trimmed = strtoupper(trim($c));
            if ($trimmed !== "") {
                $clean_codes[] = $trimmed;
            }
        }
        $clean_codes = array_unique($clean_codes);

        if (empty($clean_codes)) {
            return [];
        }

        $results = [];
        $chunks = array_chunk($clean_codes, 900);

        foreach ($chunks as $chunk) {
            $placeholders = [];
            for ($i = 0; $i < count($chunk); $i++) {
                $placeholders[] = ':c' . $i;
            }
            
            $in_clause = implode(', ', $placeholders);
            $sql = "SELECT 
                        TRIM(ITEM_CODE) as ITEM_CODE, 
                        TRIM(ITEM_NAME) as ITEM_NAME, 
                        TRIM(BARCODE) as BARCODE, 
                        PRICE, 
                        CURR_STOCK, 
                        TRIM(DEPT) as DEPT, 
                        TRIM(VC_GROUP) as VC_GROUP, 
                        TRIM(VC_SUBGROUP) as VC_SUBGROUP 
                    FROM MASTER_ITEM 
                    WHERE UPPER(ITEM_CODE) IN ($in_clause)
                    ORDER BY ITEM_CODE";
                    
            $stmt = oci_parse($conn, $sql);
            
            for ($i = 0; $i < count($chunk); $i++) {
                oci_bind_by_name($stmt, ':c' . $i, $chunk[$i]);
            }
            
            if ($stmt && @oci_execute($stmt)) {
                while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                    $results[] = [
                        'ITEM_CODE' => isset($row['ITEM_CODE']) ? $row['ITEM_CODE'] : 'N/A',
                        'ITEM_NAME' => isset($row['ITEM_NAME']) ? $row['ITEM_NAME'] : 'UNNAMED',
                        'BARCODE' => isset($row['BARCODE']) ? $row['BARCODE'] : 'N/A',
                        'PRICE' => $row['PRICE'] !== null ? floatval($row['PRICE']) : 0.00,
                        'CURR_STOCK' => $row['CURR_STOCK'] !== null ? intval($row['CURR_STOCK']) : 0,
                        'DEPT' => isset($row['DEPT']) ? $row['DEPT'] : 'GENERAL',
                        'VC_GROUP' => isset($row['VC_GROUP']) ? $row['VC_GROUP'] : 'GENERAL',
                        'VC_SUBGROUP' => isset($row['VC_SUBGROUP']) ? $row['VC_SUBGROUP'] : 'GENERAL'
                    ];
                }
            }
            if ($stmt) oci_free_statement($stmt);
        }
        return $results;
    }

    /**
     * Automatically handles dynamic DDL schema initializations and securely saves setups.
     */
    public static function saveSetupConfiguration($params) {
        $conn = Database::getConnection();

        // 1. Resolve table identifier and ensure DDL structure
        $tableIdentifier = "SHOP.MST_DEPT";
        $tableExists = false;
        $dbTableName = "MST_DEPT";
        
        $chkQuery = "SELECT table_name FROM user_tables WHERE UPPER(table_name) = 'MST_DEPT'";
        $chkStmt = oci_parse($conn, $chkQuery);
        if ($chkStmt && @oci_execute($chkStmt)) {
            if ($row = oci_fetch_array($chkStmt, OCI_ASSOC)) {
                $tableExists = true;
                $dbTableName = $row['TABLE_NAME'];
                if (preg_match('/[a-z]/', $dbTableName)) {
                    $tableIdentifier = 'SHOP."' . $dbTableName . '"';
                } else {
                    $tableIdentifier = "SHOP." . $dbTableName;
                }
            }
        }
        if ($chkStmt) oci_free_statement($chkStmt);

        if ($tableExists) {
            $colQuery = "SELECT COUNT(*) FROM user_tab_cols WHERE UPPER(table_name) = :table_name AND UPPER(column_name) = 'MAIL'";
            $colStmt = oci_parse($conn, $colQuery);
            $upperTableName = strtoupper($dbTableName);
            oci_bind_by_name($colStmt, ':table_name', $upperTableName);
            
            if ($colStmt && @oci_execute($colStmt)) {
                $row = oci_fetch_array($colStmt);
                if ($row && $row[0] == 0) {
                    $alterTableSql = "ALTER TABLE $tableIdentifier ADD MAIL VARCHAR2(100)";
                    $alterStmt = oci_parse($conn, $alterTableSql);
                    @oci_execute($alterStmt);
                    if ($alterStmt) oci_free_statement($alterStmt);
                }
            }
            if ($colStmt) oci_free_statement($colStmt);
        } else {
            $createTableSql = "
                CREATE TABLE SHOP.MST_DEPT (
                    MST_ID NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
                    SETUP_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    STOCK_DATE DATE,
                    SHOP_CODE VARCHAR2(50),
                    AUDIT_TYPE VARCHAR2(50),
                    AUDIT_MODE VARCHAR2(100),
                    SELECTED_DEPTS CLOB,
                    SELECTED_GROUPS CLOB,
                    SELECTED_SUBGROUPS CLOB,
                    MAIL VARCHAR2(100),
                    STATUS VARCHAR2(20) DEFAULT 'ACTIVE'
                )
            ";
            $createStmt = oci_parse($conn, $createTableSql);
            $createResult = @oci_execute($createStmt);
            if (!$createResult) {
                $e = oci_error($createStmt);
                throw new Exception("Failed to create Oracle table SHOP.MST_DEPT: " . $e['message']);
            }
            if ($createStmt) oci_free_statement($createStmt);
        }

        // 2. Perform safe OCI parameter-bound insertion
        $shop_code = strtoupper(trim($params['shop_code']));
        $stock_date_raw = trim($params['stock_date']);
        $audit_type = trim($params['audit_type']);
        $audit_mode = trim($params['audit_mode']);
        $depts = trim($params['depts']);
        $groups = trim($params['groups']);
        $subgroups = trim($params['subgroups']);
        $mail = trim($params['mail']);

        if (empty($shop_code) || empty($stock_date_raw) || empty($audit_type) || empty($audit_mode)) {
            throw new Exception("Missing required initialization parameters!");
        }

        $insertQuery = "
            INSERT INTO $tableIdentifier (
                STOCK_DATE, SHOP_CODE, AUDIT_TYPE, AUDIT_MODE, SELECTED_DEPTS, SELECTED_GROUPS, SELECTED_SUBGROUPS, MAIL
            ) VALUES (
                TO_DATE(:stock_date, 'YYYY-MM-DD'), :shop_code, :audit_type, :audit_mode, :depts, :groups, :subgroups, :mail
            )
        ";

        $insertStmt = oci_parse($conn, $insertQuery);
        oci_bind_by_name($insertStmt, ':stock_date', $stock_date_raw);
        oci_bind_by_name($insertStmt, ':shop_code', $shop_code);
        oci_bind_by_name($insertStmt, ':audit_type', $audit_type);
        oci_bind_by_name($insertStmt, ':audit_mode', $audit_mode);
        oci_bind_by_name($insertStmt, ':mail', $mail);
        oci_bind_by_name($insertStmt, ':depts', $depts);
        oci_bind_by_name($insertStmt, ':groups', $groups);
        oci_bind_by_name($insertStmt, ':subgroups', $subgroups);

        $executeResult = @oci_execute($insertStmt);
        if ($executeResult) {
            $commitStmt = oci_parse($conn, "COMMIT");
            oci_execute($commitStmt);
            oci_free_statement($commitStmt);
            oci_free_statement($insertStmt);
            return "ok";
        } else {
            $e = oci_error($insertStmt);
            oci_free_statement($insertStmt);
            throw new Exception("Insert Failed: " . $e['message']);
        }
    }

    /**
     * Extracts active counts and statuses from database tables.
     */
    public static function getDatabaseStats() {
        $stats = [
            'status' => 'OFFLINE',
            'host' => 'None',
            'depts' => [],
            'groups' => [],
            'subgroups' => [],
            'error' => ''
        ];

        try {
            $conn = Database::getConnection();
            if ($conn) {
                $stats['status'] = 'ONLINE';
                $stats['host'] = 'localhost';

                // 1. Departments
                $query = "SELECT DISTINCT DEPT FROM DEPTS WHERE DEPT IS NOT NULL ORDER BY DEPT";
                $stmt = @oci_parse($conn, $query);
                if ($stmt && @oci_execute($stmt)) {
                    while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                        $stats['depts'][] = trim($row['DEPT']);
                    }
                }
                if ($stmt) @oci_free_statement($stmt);

                // 2. Groups
                $query_groups = "SELECT DISTINCT VC_GROUP FROM VC_GROUPS WHERE VC_GROUP IS NOT NULL ORDER BY VC_GROUP";
                $stmt_groups = @oci_parse($conn, $query_groups);
                if ($stmt_groups && @oci_execute($stmt_groups)) {
                    while ($row = oci_fetch_array($stmt_groups, OCI_ASSOC)) {
                        $stats['groups'][] = trim($row['VC_GROUP']);
                    }
                }
                if ($stmt_groups) @oci_free_statement($stmt_groups);

                // 3. Subgroups
                $query_subs = "SELECT DISTINCT VC_SUBGROUP, VC_GROUP FROM VC_SUBGROUPS WHERE VC_SUBGROUP IS NOT NULL ORDER BY VC_GROUP, VC_SUBGROUP";
                $stmt_subs = @oci_parse($conn, $query_subs);
                if ($stmt_subs && @oci_execute($stmt_subs)) {
                    while ($row = oci_fetch_array($stmt_subs, OCI_ASSOC)) {
                        $grp = trim($row['VC_GROUP']);
                        $sub = trim($row['VC_SUBGROUP']);
                        if (!isset($stats['subgroups'][$grp])) {
                            $stats['subgroups'][$grp] = [];
                        }
                        $stats['subgroups'][$grp][] = $sub;
                    }
                }
                if ($stmt_subs) @oci_free_statement($stmt_subs);
            }
        } catch (Exception $e) {
            $stats['status'] = 'OFFLINE';
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }
}
?>
