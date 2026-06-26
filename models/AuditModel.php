<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - AUDIT MODEL
// Encapsulates all Oracle queries, dynamic lookups, and setup parameter saves.
// Uses formal Oracle PL/SQL Cursors for high-performance scoped insertions.
// ==========================================================================

require_once 'Database.php';

class AuditModel {

    /**
     * Streams a progress update to the browser during saveSetupConfiguration().
     * Format: JSON line {"p":<pct>,"ph":<phase>,"d":<description>}
     * The controller must have disabled output buffering for this to stream live.
     */
    private static function sendProgress($pct, $phase, $desc) {
        $line = json_encode(['p' => $pct, 'ph' => $phase, 'd' => $desc]);
        // Pad each line to 512 bytes — ensures the line exceeds any remaining
        // Apache/PHP buffer threshold and is delivered to the browser immediately.
        // The frontend silently ignores non-JSON trailing whitespace.
        echo $line . str_repeat(' ', max(0, 512 - strlen($line))) . "\n";
        if (ob_get_level() > 0) ob_flush();
        flush();
    }

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
                        0 as CURR_STOCK, 
                        'GENERAL' as DEPT, 
                        'GENERAL' as VC_GROUP, 
                        'GENERAL' as VC_SUBGROUP 
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
                        0 as CURR_STOCK, 
                        'GENERAL' as DEPT, 
                        'GENERAL' as VC_GROUP, 
                        'GENERAL' as VC_SUBGROUP 
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
     * Uses PL/SQL explicit CURSOR to fetch and insert records matching column definitions of MASTER_ITEM.
     */
    public static function saveSetupConfiguration($params) {
        $conn = Database::getConnection();

        // 1. Prepare and validate selected parameters
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

        // 2. Ensure both MST_DEPT and AUDIT_SETUP tables are initialized on local schema
        self::ensureTablesExist($conn);


        $archiveStmt = oci_parse($conn, "INSERT INTO SHOP.HEAD_AUDIT_ARCHIVE SELECT * FROM SHOP.HEAD_AUDIT");
        @oci_execute($archiveStmt);
        if ($archiveStmt) oci_free_statement($archiveStmt);
        // Step B: Truncate HEAD_AUDIT to clear it quickly
        $truncHeadStmt = @oci_parse($conn, "TRUNCATE TABLE SHOP.HEAD_AUDIT");
        $truncHeadSuccess = @oci_execute($truncHeadStmt);
        if ($truncHeadStmt) oci_free_statement($truncHeadStmt);
        // Step C: Fallback to DELETE if the database user doesn't have TRUNCATE permissions
        if (!$truncHeadSuccess) {
            $delHeadStmt = oci_parse($conn, "DELETE FROM SHOP.HEAD_AUDIT");
            @oci_execute($delHeadStmt);
            if ($delHeadStmt) oci_free_statement($delHeadStmt);
        }

        self::sendProgress(10, 1, "Audit data archived.");

        // 3. Clear/Truncate local table SHOP.MASTER_ITEM
        $truncStmt = @oci_parse($conn, "TRUNCATE TABLE SHOP.MASTER_ITEM");
        $truncSuccess = @oci_execute($truncStmt);
        if ($truncStmt) oci_free_statement($truncStmt);

        if (!$truncSuccess) {
            // Fallback to delete if truncate has privilege issues
            $delStmt = oci_parse($conn, "DELETE FROM SHOP.MASTER_ITEM");
            oci_execute($delStmt);
            if ($delStmt) oci_free_statement($delStmt);
        }

        self::sendProgress(18, 2, "Item master table cleared.");

        // 4. Populate categories/items scope into MASTER_ITEM from remote VS_ITEM_AUDIT@DB_LINK_SHOP using an explicit PL/SQL Cursor
        $dept_list = array_filter(array_map('trim', explode(',', $depts)));
        $group_list = array_filter(array_map('trim', explode(',', $groups)));
        $subgroup_list = array_filter(array_map('trim', explode(',', $subgroups)));

        // Select and insert records matching column definitions of MASTER_ITEM, with no table joins
        $inner_where = "";
        $where_clauses = ["A.ITEM_CODE IS NOT NULL"];
        $bind_params = [':shop_code' => substr($shop_code, 0, 10)];

        // Apply dynamic department, group, and subgroup filters hierarchically
        if ($audit_type !== 'PI') {
            $dept_filters = [];
            $bind_counter = 0;

            foreach ($dept_list as $dCode) {
                // Find all checked groups for this department
                $dGroups = [];
                foreach ($group_list as $gStr) {
                    $gParts = explode('|', $gStr);
                    if (count($gParts) === 2 && $gParts[0] === $dCode) {
                        $dGroups[] = $gParts[1];
                    }
                }

                if (empty($dGroups)) {
                    // No groups selected for this department: fetch all items for this department
                    $pName = ':dept_' . $bind_counter++;
                    $bind_params[$pName] = strtoupper($dCode);
                    $dept_filters[] = "(UPPER(TRIM(A.DEPT_CODE)) = $pName)";
                } else {
                    // Groups are selected for this department: build group filters
                    $group_filters = [];
                    foreach ($dGroups as $gCode) {
                        // Find all checked subgroups for this group under this department
                        $gSubgroups = [];
                        foreach ($subgroup_list as $sStr) {
                            $sParts = explode('|', $sStr);
                            if (count($sParts) === 3 && $sParts[0] === $dCode && $sParts[1] === $gCode) {
                                $gSubgroups[] = $sParts[2];
                            }
                        }

                        $gPlaceholder = ':grp_' . $bind_counter++;
                        $bind_params[$gPlaceholder] = strtoupper($gCode);

                        if (empty($gSubgroups)) {
                            // No subgroups selected for this group: fetch all items in this group
                            $group_filters[] = "(UPPER(TRIM(A.VC_GROUP_CODE)) = $gPlaceholder)";
                        } else {
                            // Subgroups are selected: filter by group AND subgroups IN list
                            $sub_placeholders = [];
                            foreach ($gSubgroups as $sCode) {
                                $sPlaceholder = ':sub_' . $bind_counter++;
                                $bind_params[$sPlaceholder] = strtoupper($sCode);
                                $sub_placeholders[] = $sPlaceholder;
                            }
                            $sub_chunks = array_chunk($sub_placeholders, 999);
                            $chunk_queries = [];
                            foreach ($sub_chunks as $chunk) {
                                $chunk_queries[] = "UPPER(TRIM(A.VC_SUB_GROUP_CODE)) IN (" . implode(', ', $chunk) . ")";
                            }
                            $group_filters[] = "(UPPER(TRIM(A.VC_GROUP_CODE)) = $gPlaceholder AND (" . implode(' OR ', $chunk_queries) . "))";
                        }
                    }

                    $dPlaceholder = ':dept_' . $bind_counter++;
                    $bind_params[$dPlaceholder] = strtoupper($dCode);
                    $dept_filters[] = "(UPPER(TRIM(A.DEPT_CODE)) = $dPlaceholder AND (" . implode(' OR ', $group_filters) . "))";
                }
            }

            if (!empty($dept_filters)) {
                $where_clauses[] = "(" . implode(' OR ', $dept_filters) . ")";
            }
        }

        if (!empty($where_clauses)) {
            $inner_where = " WHERE " . implode(' AND ', $where_clauses);
        }

        // Step A: Fetch filtered items from remote view and insert into local MASTER_ITEM with CURR_STOCK = 0 (no stock join to avoid hanging)
        // Note: Pulling all rows directly without ROW_NUMBER deduplication so every barcode is preserved.
        $select_query = "SELECT 
                            TRIM(A.ITEM_CODE) AS ITEM_CODE, 
                            SUBSTR(TRIM(A.ITEM_NAME), 1, 100) AS ITEM_NAME, 
                            TRIM(A.BARCODE) AS BARCODE, 
                            A.PRICE, 
                            A.DT_EFFECT_DATE, 
                            A.DEPT,
                            SUBSTR(TRIM(A.DEPT_CODE), 1, 50) AS DEPT_CODE, 
                            A.GROUPS AS VC_GROUP,
                            SUBSTR(TRIM(A.VC_GROUP_CODE), 1, 50) AS VC_GROUP_CODE, 
                            A.SUB_GROUP AS VC_SUBGROUP,
                            SUBSTR(TRIM(A.VC_SUB_GROUP_CODE), 1, 50) AS VC_SUB_GROUP_CODE,
                            A.PACK_SIZE,
                            NVL(A.CH_PI, 'N') AS CH_PI, 
                            NVL(A.CH_STATUS, 'Y') AS CH_STATUS, 
                            SUBSTR(TRIM(A.VC_UNIT), 1, 12) AS VC_UNIT,
                            :shop_code AS SHOP_CODE,
                            0 AS CURR_STOCK
                         FROM MAKESS.VS_ITEM_AUDIT@DB_LINK_SHOP A
                         $inner_where";

        // Oracle PL/SQL Block executing dynamic explicit cursor transaction inserting into Master Item
        $plsqlQuery = "
            DECLARE
                CURSOR c_items IS $select_query;
            BEGIN
                FOR r IN c_items LOOP
                    INSERT INTO SHOP.MASTER_ITEM (
                        ITEM_CODE, ITEM_NAME, BARCODE, PRICE, DT_EFFECT_DATE, DEPT, DEPT_CODE, VC_GROUP, VC_GROUP_CODE, VC_SUBGROUP, VC_SUB_GROUP_CODE, PACK_SIZE, CH_PI, CH_STATUS, VC_UNIT, SHOP_CODE, CURR_STOCK
                    ) VALUES (
                        r.ITEM_CODE, r.ITEM_NAME, r.BARCODE, r.PRICE, r.DT_EFFECT_DATE, r.DEPT, r.DEPT_CODE, r.VC_GROUP, r.VC_GROUP_CODE, r.VC_SUBGROUP, r.VC_SUB_GROUP_CODE, r.PACK_SIZE, r.CH_PI, r.CH_STATUS, r.VC_UNIT, r.SHOP_CODE, r.CURR_STOCK
                    );
                END LOOP;
                COMMIT;
            END;
        ";

        $bulkStmt = oci_parse($conn, $plsqlQuery);
        if (!$bulkStmt) {
            $e = oci_error($conn);
            throw new Exception("PL/SQL parsing failed: " . $e['message']);
        }

        // Correct OCI8 binding loop using direct array element referencing to prevent variable pointer collisions
        foreach ($bind_params as $placeholder => $val) {
            oci_bind_by_name($bulkStmt, $placeholder, $bind_params[$placeholder]);
        }

        self::sendProgress(22, 3, "Syncing items from ERP — please wait...");

        $bulkExec = @oci_execute($bulkStmt);
        if (!$bulkExec) {
            $e = oci_error($bulkStmt);
            if ($bulkStmt) oci_free_statement($bulkStmt);
            throw new Exception("Cursor-based transactional insertion failed: " . $e['message']);
        }
        if ($bulkStmt) oci_free_statement($bulkStmt);

        self::sendProgress(68, 3, "Items synced from ERP.");

        // Step B: Update CURR_STOCK by calling makess.get_shop_stock@db_link_shop per item via cursor loop
        $stockUpdateQuery = "
            DECLARE
                CURSOR c_stock IS SELECT DISTINCT ITEM_CODE FROM SHOP.MASTER_ITEM;
                V_STK NUMBER := 0;
            BEGIN
                FOR I IN c_stock LOOP
                    V_STK := 0;
                    SELECT makess.get_shop_stock@db_link_shop('01', :shop_code_stk, I.ITEM_CODE)
                    INTO V_STK
                    FROM DUAL;
                    UPDATE SHOP.MASTER_ITEM S
                    SET S.CURR_STOCK = NVL(V_STK, 0)
                    WHERE S.ITEM_CODE = I.ITEM_CODE;
                END LOOP;
                COMMIT;
            END;
        ";

        $stockStmt = oci_parse($conn, $stockUpdateQuery);
        if (!$stockStmt) {
            $e = oci_error($conn);
            throw new Exception("PL/SQL parsing failed for stock update: " . $e['message']);
        }
        $shop_code_stk = substr($shop_code, 0, 5);
        oci_bind_by_name($stockStmt, ':shop_code_stk', $shop_code_stk);

        self::sendProgress(72, 4, "Syncing current stock quantities...");

        $stockExec = @oci_execute($stockStmt);
        if (!$stockExec) {
            $e = oci_error($stockStmt);
            if ($stockStmt) oci_free_statement($stockStmt);
            throw new Exception("Cursor-based stock update failed: " . $e['message']);
        }
        if ($stockStmt) oci_free_statement($stockStmt);

        self::sendProgress(92, 4, "Stock quantities synced.");

        // 5. Insert active setup parameters configuration row in SHOP.AUDIT_SETUP
        self::sendProgress(96, 5, "Saving configuration & finalising...");

        $insertQuery = "
            INSERT INTO SHOP.AUDIT_SETUP (
                STOCK_DATE, SHOP_CODE, AUDIT_TYPE, AUDIT_MODE, SELECTED_DEPTS, SELECTED_GROUPS, SELECTED_SUBGROUPS, MAIL
            ) VALUES (
                TO_DATE(:stock_date, 'YYYY-MM-DD'), :shop_code_param, :audit_type, :audit_mode, empty_clob(), empty_clob(), empty_clob(), :mail
            ) RETURNING SELECTED_DEPTS, SELECTED_GROUPS, SELECTED_SUBGROUPS INTO :depts_clob, :groups_clob, :subgroups_clob
        ";

        $insertStmt = oci_parse($conn, $insertQuery);
        oci_bind_by_name($insertStmt, ':stock_date', $stock_date_raw);
        oci_bind_by_name($insertStmt, ':shop_code_param', $shop_code);
        oci_bind_by_name($insertStmt, ':audit_type', $audit_type);
        oci_bind_by_name($insertStmt, ':audit_mode', $audit_mode);
        oci_bind_by_name($insertStmt, ':mail', $mail);

        $clobDepts = oci_new_descriptor($conn, OCI_D_LOB);
        $clobGroups = oci_new_descriptor($conn, OCI_D_LOB);
        $clobSubgroups = oci_new_descriptor($conn, OCI_D_LOB);

        oci_bind_by_name($insertStmt, ':depts_clob', $clobDepts, -1, OCI_B_CLOB);
        oci_bind_by_name($insertStmt, ':groups_clob', $clobGroups, -1, OCI_B_CLOB);
        oci_bind_by_name($insertStmt, ':subgroups_clob', $clobSubgroups, -1, OCI_B_CLOB);

        $executeResult = @oci_execute($insertStmt, OCI_DEFAULT);
        if (!$executeResult) {
            $e = oci_error($insertStmt);
            oci_free_statement($insertStmt);
            throw new Exception("Setup parameters insertion failed: " . $e['message']);
        }

        if ($clobDepts && !empty($depts)) $clobDepts->save($depts);
        if ($clobGroups && !empty($groups)) $clobGroups->save($groups);
        if ($clobSubgroups && !empty($subgroups)) $clobSubgroups->save($subgroups);

        oci_free_statement($insertStmt);

        // Commit transaction
        $commitStmt = oci_parse($conn, "COMMIT");
        oci_execute($commitStmt);
        oci_free_statement($commitStmt);

        return "ok";
    }

    /**
     * Retrieves the sync summary stats from the newly populated MASTER_ITEM table.
     */
    public static function getSyncSummary() {
        $conn = Database::getConnection();
        $query = "SELECT 
                    COUNT(ITEM_CODE) AS NO_OF_ITEMS,
                    SUM(NVL(CURR_STOCK, 0)) AS TOTAL_QTY,
                    SUM(NVL(PRICE, 0) * NVL(CURR_STOCK, 0)) AS TOTAL_VALUE 
                  FROM (
                      SELECT DISTINCT ITEM_CODE, PRICE, CURR_STOCK 
                      FROM SHOP.MASTER_ITEM
                      WHERE NVL(CURR_STOCK, 0) != 0
                  )";
        $stmt = oci_parse($conn, $query);

        $no_of_items = 0;
        $total_qty = 0;
        $total_value = 0.0;
        if ($stmt && @oci_execute($stmt)) {
            if ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                $no_of_items = isset($row['NO_OF_ITEMS']) ? (int)$row['NO_OF_ITEMS'] : 0;
                $total_qty = isset($row['TOTAL_QTY']) ? round((float)$row['TOTAL_QTY']) : 0;
                $total_value = isset($row['TOTAL_VALUE']) ? (float)$row['TOTAL_VALUE'] : 0.0;
            }
            oci_free_statement($stmt);
        }

        // Fetch dynamic department breakdown from MASTER_ITEM joined with local MST_DEPT for description lookups
        $depts = [];
        $deptQuery = "SELECT 
                        NVL(D.DEPT, M.DEPT_CODE) AS DEPT_NAME,
                        COUNT(M.ITEM_CODE) AS NO_OF_ITEMS,
                        SUM(NVL(M.CURR_STOCK, 0)) AS TOTAL_QTY,
                        SUM(NVL(M.PRICE, 0) * NVL(M.CURR_STOCK, 0)) AS TOTAL_VALUE
                      FROM (
                          SELECT DISTINCT ITEM_CODE, PRICE, CURR_STOCK, DEPT_CODE 
                          FROM SHOP.MASTER_ITEM
                          WHERE NVL(CURR_STOCK, 0) != 0
                      ) M
                      LEFT JOIN (
                          SELECT DISTINCT DEPT_CODE, DEPT FROM SHOP.MST_DEPT
                      ) D ON D.DEPT_CODE = M.DEPT_CODE
                      GROUP BY D.DEPT, M.DEPT_CODE
                      ORDER BY DEPT_NAME";
        $deptStmt = oci_parse($conn, $deptQuery);
        if ($deptStmt && @oci_execute($deptStmt)) {
            while ($row = oci_fetch_array($deptStmt, OCI_ASSOC)) {
                $depts[] = [
                    'dept_name' => isset($row['DEPT_NAME']) ? trim($row['DEPT_NAME']) : 'UNKNOWN',
                    'no_of_items' => isset($row['NO_OF_ITEMS']) ? (int)$row['NO_OF_ITEMS'] : 0,
                    'total_qty' => isset($row['TOTAL_QTY']) ? round((float)$row['TOTAL_QTY']) : 0,
                    'total_value' => isset($row['TOTAL_VALUE']) ? (float)$row['TOTAL_VALUE'] : 0.0
                ];
            }
            oci_free_statement($deptStmt);
        }

        return [
            'status' => 'ok',
            'no_of_items' => $no_of_items,
            'total_qty' => $total_qty,
            'total_value' => $total_value,
            'depts' => $depts
        ];
    }


    /**
     * Look up shop details in the remote MST_SHOP database link table.
     */
    public static function lookupShopCode($shop_code) {
        $conn = Database::getConnection();
        $code = strtoupper(trim($shop_code));
        if (empty($code)) {
            return ['status' => 'error', 'message' => 'Shop code is empty.'];
        }

        $sql = "SELECT TRIM(VC_SHOP_DESC) AS VC_SHOP_DESC 
                FROM POS.MST_SHOP@DB_LINK_SHOP 
                WHERE UPPER(TRIM(VC_SHOP_CODE)) = :shop_code";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':shop_code', $code);

        if ($stmt && @oci_execute($stmt)) {
            if ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                $desc = isset($row['VC_SHOP_DESC']) ? trim($row['VC_SHOP_DESC']) : 'UNKNOWN SHOP';
                oci_free_statement($stmt);
                return [
                    'status' => 'success',
                    'shop_code' => $code,
                    'shop_desc' => $desc
                ];
            }
        }
        
        if ($stmt) oci_free_statement($stmt);
        return [
            'status' => 'not_found',
            'message' => 'Shop Code not registered.'
        ];
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
            'tree' => [],
            'error' => ''
        ];

        try {
            $conn = Database::getConnection();
            if ($conn) {
                $stats['status'] = 'ONLINE';
                $stats['host'] = '10.10.0.202';

                // 1. Ensure table schemas exist on local schema
                self::ensureTablesExist($conn);

                // 2. Check if local categories table is unpopulated
                $countQuery = "SELECT COUNT(*) AS CNT FROM SHOP.MST_DEPT";
                $countStmt = oci_parse($conn, $countQuery);
                $isPopulated = false;
                if ($countStmt && @oci_execute($countStmt)) {
                    if ($row = oci_fetch_array($countStmt, OCI_ASSOC)) {
                        if (intval($row['CNT']) > 0) {
                            $isPopulated = true;
                        }
                    }
                }
                if ($countStmt) oci_free_statement($countStmt);

                // 3. If empty, automatically pull from remote database link to initialize local MST_DEPT
                if (!$isPopulated) {
                    try {
                        self::syncMasterDepartments($conn);
                    } catch (Exception $e) {
                        // If remote link is down but we have some previous data, log it, otherwise throw
                        if (!$isPopulated) {
                            throw $e;
                        }
                    }
                }

                // 4. Query all departments, groups, and subgroups locally from local SHOP.MST_DEPT
                $query = "SELECT DISTINCT 
                            TRIM(DEPT_CODE) AS DEPT_CODE, 
                            TRIM(DEPT) AS DEPT, 
                            TRIM(VC_GROUP_CODE) AS VC_GROUP_CODE, 
                            TRIM(GROUPS) AS GROUPS, 
                            TRIM(VC_SUB_GROUP_CODE) AS VC_SUB_GROUP_CODE, 
                            TRIM(SUB_GROUP) AS SUB_GROUP
                          FROM SHOP.MST_DEPT
                          WHERE DEPT_CODE IS NOT NULL 
                            AND VC_GROUP_CODE IS NOT NULL 
                            AND VC_SUB_GROUP_CODE IS NOT NULL
                          ORDER BY DEPT, GROUPS, SUB_GROUP";

                $stmt = @oci_parse($conn, $query);
                if ($stmt && @oci_execute($stmt)) {
                    $tree = [];
                    $flat_depts = [];
                    $flat_groups = [];
                    $flat_subgroups = [];

                    while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                        $dept_code = trim($row['DEPT_CODE']);
                        $dept_desc = trim($row['DEPT']);
                        $group_code = trim($row['VC_GROUP_CODE']);
                        $group_desc = trim($row['GROUPS']);
                        $sub_code = trim($row['VC_SUB_GROUP_CODE']);
                        $sub_desc = trim($row['SUB_GROUP']);

                        if (!in_array($dept_desc, $flat_depts)) {
                            $flat_depts[] = $dept_desc;
                        }
                        if (!in_array($group_desc, $flat_groups)) {
                            $flat_groups[] = $group_desc;
                        }
                        if (!isset($flat_subgroups[$group_desc])) {
                            $flat_subgroups[$group_desc] = [];
                        }
                        if (!in_array($sub_desc, $flat_subgroups[$group_desc])) {
                            $flat_subgroups[$group_desc][] = $sub_desc;
                        }

                        if (!isset($tree[$dept_code])) {
                            $tree[$dept_code] = [
                                'id' => $dept_code,
                                'name' => $dept_desc,
                                'groups' => []
                            ];
                        }

                        if (!isset($tree[$dept_code]['groups'][$group_code])) {
                            $tree[$dept_code]['groups'][$group_code] = [
                                'id' => $group_code,
                                'name' => $group_desc,
                                'subgroups' => []
                            ];
                        }

                        $sub_exists = false;
                        foreach ($tree[$dept_code]['groups'][$group_code]['subgroups'] as $existing_sub) {
                            if ($existing_sub['id'] === $sub_code) {
                                $sub_exists = true;
                                break;
                            }
                        }
                        if (!$sub_exists) {
                            $tree[$dept_code]['groups'][$group_code]['subgroups'][] = [
                                'id' => $sub_code,
                                'name' => $sub_desc
                            ];
                        }
                    }

                    $json_tree = [];
                    foreach ($tree as $dCode => $dData) {
                        $groups_list = [];
                        foreach ($dData['groups'] as $gDesc => $gData) {
                            $groups_list[] = $gData;
                        }
                        $dData['groups'] = $groups_list;
                        $json_tree[] = $dData;
                    }

                    $stats['depts'] = $flat_depts;
                    $stats['groups'] = $flat_groups;
                    $stats['subgroups'] = $flat_subgroups;
                    $stats['tree'] = $json_tree;
                }
                if ($stmt) @oci_free_statement($stmt);
            }
        } catch (Exception $e) {
            $stats['status'] = 'OFFLINE';
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }

    /**
     * Handles retrieving scoped items for dynamic Step 8 summary/preview.
     * Contains exactly 16 columns structure and strictly has NO mock/simulated fallbacks.
     */
    public static function getScopedItemsPreview($params) {
        $conn = Database::getConnection();
        $audit_type = trim($params['audit_type']);
        $depts = trim($params['depts']);
        $groups = trim($params['groups']);
        $subgroups = trim($params['subgroups']);
        $shop_code = isset($params['shop_code']) ? strtoupper(trim($params['shop_code'])) : 'SC001';

        $dept_list = array_filter(array_map('trim', explode(',', $depts)));
        $group_list = array_filter(array_map('trim', explode(',', $groups)));
        $subgroup_list = array_filter(array_map('trim', explode(',', $subgroups)));

        $select_query = "SELECT DISTINCT 
                            TRIM(A.ITEM_CODE) AS ITEM_CODE, 
                            SUBSTR(TRIM(A.ITEM_NAME), 1, 100) AS ITEM_NAME, 
                            TRIM(A.BARCODE) AS BARCODE, 
                            A.PRICE, 
                            A.DT_EFFECT_DATE, 
                            SUBSTR(TRIM(A.DEPT), 1, 100) AS DEPT, 
                            SUBSTR(TRIM(A.DEPT_CODE), 1, 50) AS DEPT_CODE, 
                            SUBSTR(TRIM(A.GROUPS), 1, 100) AS GROUPS, 
                            SUBSTR(TRIM(A.VC_GROUP_CODE), 1, 50) AS VC_GROUP_CODE, 
                            SUBSTR(TRIM(A.SUB_GROUP), 1, 100) AS SUB_GROUP,
                            SUBSTR(TRIM(A.VC_SUB_GROUP_CODE), 1, 50) AS VC_SUB_GROUP_CODE,
                            A.PACK_SIZE,
                            NVL(A.CH_PI, 'N') AS CH_PI, 
                            NVL(A.CH_STATUS, 'Y') AS CH_STATUS, 
                            SUBSTR(TRIM(A.VC_UNIT), 1, 12) AS VC_UNIT
                         FROM MAKESS.VS_ITEM_AUDIT@DB_LINK_SHOP A";

        $where_clauses = ["A.ITEM_CODE IS NOT NULL"];
        $bind_params = [];

        // Apply dynamic department, group, and subgroup filters hierarchically
        if ($audit_type !== 'PI') {
            $dept_filters = [];
            $bind_counter = 0;

            foreach ($dept_list as $dCode) {
                // Find all checked groups for this department
                $dGroups = [];
                foreach ($group_list as $gStr) {
                    $gParts = explode('|', $gStr);
                    if (count($gParts) === 2 && $gParts[0] === $dCode) {
                        $dGroups[] = $gParts[1];
                    }
                }

                if (empty($dGroups)) {
                    // No groups selected for this department: fetch all items for this department
                    $pName = ':dept_' . $bind_counter++;
                    $bind_params[$pName] = $dCode;
                    $dept_filters[] = "(TRIM(A.DEPT_CODE) = $pName)";
                } else {
                    // Groups are selected for this department: build group filters
                    $group_filters = [];
                    foreach ($dGroups as $gCode) {
                        // Find all checked subgroups for this group under this department
                        $gSubgroups = [];
                        foreach ($subgroup_list as $sStr) {
                            $sParts = explode('|', $sStr);
                            if (count($sParts) === 3 && $sParts[0] === $dCode && $sParts[1] === $gCode) {
                                $gSubgroups[] = $sParts[2];
                            }
                        }

                        $gPlaceholder = ':grp_' . $bind_counter++;
                        $bind_params[$gPlaceholder] = $gCode;

                        if (empty($gSubgroups)) {
                            // No subgroups selected for this group: fetch all items in this group
                            $group_filters[] = "(TRIM(A.VC_GROUP_CODE) = $gPlaceholder)";
                        } else {
                            // Subgroups are selected: filter by group AND subgroups IN list
                            $sub_placeholders = [];
                            foreach ($gSubgroups as $sCode) {
                                $sPlaceholder = ':sub_' . $bind_counter++;
                                $bind_params[$sPlaceholder] = $sCode;
                                $sub_placeholders[] = $sPlaceholder;
                            }
                            $sub_chunks = array_chunk($sub_placeholders, 999);
                            $chunk_queries = [];
                            foreach ($sub_chunks as $chunk) {
                                $chunk_queries[] = "TRIM(A.VC_SUB_GROUP_CODE) IN (" . implode(', ', $chunk) . ")";
                            }
                            $group_filters[] = "(TRIM(A.VC_GROUP_CODE) = $gPlaceholder AND (" . implode(' OR ', $chunk_queries) . "))";
                        }
                    }

                    $dPlaceholder = ':dept_' . $bind_counter++;
                    $bind_params[$dPlaceholder] = $dCode;
                    $dept_filters[] = "(TRIM(A.DEPT_CODE) = $dPlaceholder AND (" . implode(' OR ', $group_filters) . "))";
                }
            }

            if (!empty($dept_filters)) {
                $where_clauses[] = "(" . implode(' OR ', $dept_filters) . ")";
            }
        }

        if (!empty($where_clauses)) {
            $select_query .= " WHERE " . implode(' AND ', $where_clauses);
        }

        // Limit to 200 items for smooth visual rendering and quick lookup in preview
        $select_query = "SELECT * FROM ($select_query) WHERE ROWNUM <= 200";

        $stmt = oci_parse($conn, $select_query);
        if (!$stmt) {
            $e = oci_error($conn);
            throw new Exception("Oracle SQL parsing failed: " . $e['message']);
        }

        // Correct OCI8 binding loop using direct array element referencing to prevent variable pointer collisions
        foreach ($bind_params as $placeholder => $val) {
            oci_bind_by_name($stmt, $placeholder, $bind_params[$placeholder]);
        }

        $results = [];
        $exec = @oci_execute($stmt);
        if (!$exec) {
            $e = oci_error($stmt);
            throw new Exception("Oracle SQL execution failed: " . $e['message'] . "\nQuery: " . $select_query);
        }

        while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
            $results[] = [
                'ITEM_CODE' => isset($row['ITEM_CODE']) ? trim($row['ITEM_CODE']) : 'N/A',
                'ITEM_NAME' => isset($row['ITEM_NAME']) ? trim($row['ITEM_NAME']) : 'UNNAMED',
                'BARCODE' => isset($row['BARCODE']) ? trim($row['BARCODE']) : 'N/A',
                'PRICE' => $row['PRICE'] !== null ? floatval($row['PRICE']) : 0.00,
                'DT_EFFECT_DATE' => isset($row['DT_EFFECT_DATE']) ? trim($row['DT_EFFECT_DATE']) : 'N/A',
                'DEPT' => isset($row['DEPT']) ? trim($row['DEPT']) : 'N/A',
                'DEPT_CODE' => isset($row['DEPT_CODE']) ? trim($row['DEPT_CODE']) : 'N/A',
                'GROUPS' => isset($row['GROUPS']) ? trim($row['GROUPS']) : 'N/A',
                'VC_GROUP_CODE' => isset($row['VC_GROUP_CODE']) ? trim($row['VC_GROUP_CODE']) : 'N/A',
                'SUB_GROUP' => isset($row['SUB_GROUP']) ? trim($row['SUB_GROUP']) : 'N/A',
                'VC_SUB_GROUP_CODE' => isset($row['VC_SUB_GROUP_CODE']) ? trim($row['VC_SUB_GROUP_CODE']) : 'N/A',
                'PACK_SIZE' => $row['PACK_SIZE'] !== null ? floatval($row['PACK_SIZE']) : 0.000,
                'CH_PI' => isset($row['CH_PI']) ? trim($row['CH_PI']) : 'N',
                'CH_STATUS' => isset($row['CH_STATUS']) ? trim($row['CH_STATUS']) : 'Y',
                'VC_UNIT' => isset($row['VC_UNIT']) ? trim($row['VC_UNIT']) : 'N/A'
            ];
        }
        oci_free_statement($stmt);

        if (empty($results)) {
            $bind_str = [];
            foreach ($bind_params as $k => $v) {
                $bind_str[] = "$k => '$v'";
            }
            throw new Exception("Database Scoping Empty: No matching items found in the VS_ITEM_AUDIT view for the selected departments, groups, or subgroups.\n\n[DIAGNOSTICS]\nQuery:\n" . $select_query . "\n\nBinds:\n" . implode(', ', $bind_str));
        }

        return $results;
    }

    /**
     * Synchronizes all departments, groups, and subgroups from the remote view
     * into the local SHOP.MST_DEPT table using an explicit PL/SQL cursor loop.
     */
    public static function syncMasterDepartments($conn = null) {
        if ($conn === null) {
            $conn = Database::getConnection();
        }

        // Ensure table structure is present
        self::ensureTablesExist($conn);

        // Clear/Truncate local table SHOP.MST_DEPT
        $truncStmt = @oci_parse($conn, "TRUNCATE TABLE SHOP.MST_DEPT");
        $truncSuccess = @oci_execute($truncStmt);
        if ($truncStmt) oci_free_statement($truncStmt);

        if (!$truncSuccess) {
            $delStmt = oci_parse($conn, "DELETE FROM SHOP.MST_DEPT");
            oci_execute($delStmt);
            if ($delStmt) oci_free_statement($delStmt);
        }

        // Sync from remote database view using explicit PL/SQL cursor transaction
        $plsqlSync = "
            DECLARE
                CURSOR c_depts IS 
                    SELECT DISTINCT 
                        TRIM(DEPT_CODE) AS DEPT_CODE, 
                        TRIM(DEPT) AS DEPT, 
                        TRIM(VC_GROUP_CODE) AS VC_GROUP_CODE, 
                        TRIM(GROUPS) AS GROUPS, 
                        TRIM(VC_SUB_GROUP_CODE) AS VC_SUB_GROUP_CODE, 
                        TRIM(SUB_GROUP) AS SUB_GROUP
                    FROM VW_STK_DEPT@DB_LINK_SHOP
                    WHERE DEPT_CODE IS NOT NULL 
                      AND VC_GROUP_CODE IS NOT NULL 
                      AND VC_SUB_GROUP_CODE IS NOT NULL;
            BEGIN
                FOR r IN c_depts LOOP
                    INSERT INTO SHOP.MST_DEPT (
                        DEPT_CODE, DEPT, VC_GROUP_CODE, GROUPS, VC_SUB_GROUP_CODE, SUB_GROUP
                    ) VALUES (
                        r.DEPT_CODE, r.DEPT, r.VC_GROUP_CODE, r.GROUPS, r.VC_SUB_GROUP_CODE, r.SUB_GROUP
                    );
                END LOOP;
                COMMIT;
            END;
        ";

        $syncStmt = oci_parse($conn, $plsqlSync);
        if (!$syncStmt) {
            $e = oci_error($conn);
            throw new Exception("PL/SQL parsing failed for departments sync: " . $e['message']);
        }

        $syncExec = @oci_execute($syncStmt);
        if (!$syncExec) {
            $e = oci_error($syncStmt);
            oci_free_statement($syncStmt);
            throw new Exception("Cursor-based dynamic department synchronization failed: " . $e['message']);
        }
        oci_free_statement($syncStmt);

        // Commit transaction
        $commitStmt = oci_parse($conn, "COMMIT");
        oci_execute($commitStmt);
        oci_free_statement($commitStmt);

        return true;
    }

    /**
     * Self-healing table check/initializer. Ensures that local tables MST_DEPT
     * and AUDIT_SETUP exist on the local database schema on runtime.
     */
    private static function ensureTablesExist($conn) {
        // 1. Resolve or create standard Master Categories table SHOP.MST_DEPT
        $mstDeptExists = false;
        $chkDept = "SELECT table_name FROM user_tables WHERE UPPER(table_name) = 'MST_DEPT'";
        $chkDeptStmt = oci_parse($conn, $chkDept);
        if ($chkDeptStmt && @oci_execute($chkDeptStmt)) {
            if (oci_fetch_array($chkDeptStmt, OCI_ASSOC)) {
                $mstDeptExists = true;
            }
        }
        if ($chkDeptStmt) oci_free_statement($chkDeptStmt);

        if (!$mstDeptExists) {
            $createMstDept = "
                CREATE TABLE SHOP.MST_DEPT (
                    DEPT_ID NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
                    DEPT_CODE VARCHAR2(50),
                    DEPT VARCHAR2(100),
                    VC_GROUP_CODE VARCHAR2(50),
                    GROUPS VARCHAR2(100),
                    VC_SUB_GROUP_CODE VARCHAR2(50),
                    SUB_GROUP VARCHAR2(100),
                    SYNC_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ";
            $createStmt = oci_parse($conn, $createMstDept);
            $createResult = @oci_execute($createStmt);
            if (!$createResult) {
                $e = oci_error($createStmt);
                oci_free_statement($createStmt);
                throw new Exception("Failed to initialize Master categories table SHOP.MST_DEPT: " . $e['message']);
            }
            oci_free_statement($createStmt);
        } else {
            // Check if it's the old schema (which had CLOBs like SELECTED_DEPTS instead of individual codes)
            // If it is, drop it and recreate it to store individual synchronized codes
            $colCheck = "SELECT column_name FROM user_tab_cols WHERE UPPER(table_name) = 'MST_DEPT' AND UPPER(column_name) = 'SELECTED_DEPTS'";
            $colStmt = oci_parse($conn, $colCheck);
            $isOldSchema = false;
            if ($colStmt && @oci_execute($colStmt)) {
                if (oci_fetch_array($colStmt, OCI_ASSOC)) {
                    $isOldSchema = true;
                }
            }
            if ($colStmt) oci_free_statement($colStmt);

            if ($isOldSchema) {
                // Drop and recreate table
                $dropStmt = oci_parse($conn, "DROP TABLE SHOP.MST_DEPT CASCADE CONSTRAINTS");
                @oci_execute($dropStmt);
                if ($dropStmt) oci_free_statement($dropStmt);

                $createMstDept = "
                    CREATE TABLE SHOP.MST_DEPT (
                        DEPT_ID NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
                        DEPT_CODE VARCHAR2(50),
                        DEPT VARCHAR2(100),
                        VC_GROUP_CODE VARCHAR2(50),
                        GROUPS VARCHAR2(100),
                        VC_SUB_GROUP_CODE VARCHAR2(50),
                        SUB_GROUP VARCHAR2(100),
                        SYNC_TIME TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ";
                $createStmt = oci_parse($conn, $createMstDept);
                $createResult = @oci_execute($createStmt);
                if (!$createResult) {
                    $e = oci_error($createStmt);
                    oci_free_statement($createStmt);
                    throw new Exception("Failed to recreate table SHOP.MST_DEPT with master list schema: " . $e['message']);
                }
                oci_free_statement($createStmt);
            }
        }

        // 2. Resolve or create setup parameters logging table SHOP.AUDIT_SETUP
        $auditSetupExists = false;
        $chkSetup = "SELECT table_name FROM user_tables WHERE UPPER(table_name) = 'AUDIT_SETUP'";
        $chkSetupStmt = oci_parse($conn, $chkSetup);
        if ($chkSetupStmt && @oci_execute($chkSetupStmt)) {
            if (oci_fetch_array($chkSetupStmt, OCI_ASSOC)) {
                $auditSetupExists = true;
            }
        }
        if ($chkSetupStmt) oci_free_statement($chkSetupStmt);

        if (!$auditSetupExists) {
            $createAuditSetup = "
                CREATE TABLE SHOP.AUDIT_SETUP (
                    SETUP_ID NUMBER GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
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
            $createStmt = oci_parse($conn, $createAuditSetup);
            $createResult = @oci_execute($createStmt);
            if (!$createResult) {
                $e = oci_error($createStmt);
                oci_free_statement($createStmt);
                throw new Exception("Failed to initialize setup logs table SHOP.AUDIT_SETUP: " . $e['message']);
            }
            oci_free_statement($createStmt);
        }

        // 3. Resolve or create HEAD_AUDIT_ARCHIVE table
        $auditArchiveExists = false;
        $chkArchive = "SELECT table_name FROM user_tables WHERE UPPER(table_name) = 'HEAD_AUDIT_ARCHIVE'";
        $chkArchiveStmt = oci_parse($conn, $chkArchive);
        if ($chkArchiveStmt && @oci_execute($chkArchiveStmt)) {
            if (oci_fetch_array($chkArchiveStmt, OCI_ASSOC)) {
                $auditArchiveExists = true;
            }
        }
        if ($chkArchiveStmt) oci_free_statement($chkArchiveStmt);

        if (!$auditArchiveExists) {
            $createArchive = "
                CREATE TABLE SHOP.HEAD_AUDIT_ARCHIVE (
                    SHOP_CODE VARCHAR2(20),
                    ITEM_CODE VARCHAR2(20),
                    QTY FLOAT,
                    DATE_SYS VARCHAR2(50),
                    EMP_CODE VARCHAR2(20),
                    IP VARCHAR2(50),
                    USER_NAME VARCHAR2(50),
                    RACK_NUM VARCHAR2(50),
                    AUDIT_ROUND NUMBER,
                    ARCHIVE_TIMESTAMP TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ";
            $createStmt = oci_parse($conn, $createArchive);
            $createResult = @oci_execute($createStmt);
            if (!$createResult) {
                $e = oci_error($createStmt);
                oci_free_statement($createStmt);
                throw new Exception("Failed to initialize archive table SHOP.HEAD_AUDIT_ARCHIVE: " . $e['message']);
            }
            oci_free_statement($createStmt);
        }

        // Self-healing: Recompile the ZS_VW_AUDIT_PENDING view in case it became invalid
        // due to dropping/recreating the MST_DEPT table. This fixes ORA-04063 errors in the old system.
        $compileStmt = @oci_parse($conn, "ALTER VIEW SHOP.ZS_VW_AUDIT_PENDING COMPILE");
        @oci_execute($compileStmt);
        if ($compileStmt) oci_free_statement($compileStmt);
    }

    /**
     * Safely triggers the initialization boundaries of a physical inventory stock-take.
     * Archives the old session, clears buffers, sets the ACTIVE shop contexts, and logs the session.
     */
    public static function initializeStockTakeSession($shopCode, $adminUsername) {
        $conn = Database::getConnection();
        if (!$conn) {
            throw new Exception("Oracle database connection failed during session initialization.");
        }

        // Ensure prerequisite tables exist
        self::ensureTablesExist($conn);

        // 1. Archive previous stock-take data
        // Using exact old logic to ensure all columns (including AUDIT_ROUND) are captured properly
        $archiveSql = "INSERT INTO SHOP.HEAD_AUDIT_ARCHIVE SELECT * FROM SHOP.HEAD_AUDIT";
        
        $archiveStmt = oci_parse($conn, $archiveSql);
        if (!@oci_execute($archiveStmt)) {
            $e = oci_error($archiveStmt);
            oci_free_statement($archiveStmt);
            throw new Exception("Failed to archive HEAD_AUDIT: " . $e['message']);
        }
        oci_free_statement($archiveStmt);

        // 2. Clear buffers (TRUNCATE)
        $truncHeadStmt = @oci_parse($conn, "TRUNCATE TABLE SHOP.HEAD_AUDIT");
        @oci_execute($truncHeadStmt);
        if ($truncHeadStmt) oci_free_statement($truncHeadStmt);

        // $truncShopStmt = @oci_parse($conn, "TRUNCATE TABLE SHOP.MASTER_SHOP");
        // @oci_execute($truncShopStmt);
        // if ($truncShopStmt) oci_free_statement($truncShopStmt);

        // 3. Initialize Active Shop
        // We set ACTIVE=1 and AUDIT_SYS='1' to perfectly emulate the old system's start procedures
        $initShopSql = "INSERT INTO SHOP.MASTER_SHOP (SHOP_CODE, SHOP_NAME, ACTIVE, AUDIT_SYS) 
                        VALUES (:code, :name, 1, '1')";
        $initShopStmt = oci_parse($conn, $initShopSql);
        oci_bind_by_name($initShopStmt, ':code', $shopCode);
        oci_bind_by_name($initShopStmt, ':name', $shopCode); // Using shop code as name fallback
        if (!@oci_execute($initShopStmt)) {
            $e = oci_error($initShopStmt);
            oci_free_statement($initShopStmt);
            throw new Exception("Failed to initialize MASTER_SHOP: " . $e['message']);
        }
        oci_free_statement($initShopStmt);

        // 4. Log the Session to STK_LOG
        // The manager requested REMARKS to simply be the Shop Code
        $logSql = "INSERT INTO STK_LOG@DB_LINK_SHOP (SHOPCODE, START_BY, STATUS, REMARKS, START_DATE, END_DATE) 
                   VALUES (:code, :admin, 'A', :remarks, SYSDATE, SYSDATE)";
        $logStmt = oci_parse($conn, $logSql);
        oci_bind_by_name($logStmt, ':code', $shopCode);
        oci_bind_by_name($logStmt, ':admin', $adminUsername);
        oci_bind_by_name($logStmt, ':remarks', $shopCode);
        
        if (!@oci_execute($logStmt)) {
            $e = oci_error($logStmt);
            oci_free_statement($logStmt);
            // It is safe to just ignore the remote DB_LINK errors or throw them if it's critical. 
            // We'll throw to be safe and let it be logged.
            throw new Exception("Failed to record STK_LOG session: " . $e['message']);
        }
        oci_free_statement($logStmt);

        return true;
    }
}
?>
