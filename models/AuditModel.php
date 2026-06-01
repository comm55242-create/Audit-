<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - AUDIT MODEL
// Encapsulates all Oracle queries, dynamic lookups, and setup parameter saves.
// Uses formal Oracle PL/SQL Cursors for high-performance scoped insertions.
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

        // 4. Populate categories/items scope into MASTER_ITEM from remote VS_ITEM_AUDIT@DB_LINK_SHOP using an explicit PL/SQL Cursor
        $dept_list = array_filter(array_map('trim', explode(',', $depts)));
        $group_list = array_filter(array_map('trim', explode(',', $groups)));
        $subgroup_list = array_filter(array_map('trim', explode(',', $subgroups)));

        // Select and insert records matching column definitions of MASTER_ITEM, with no table joins and zero stock fallback to avoid invalid DB link functions
        $select_query = "SELECT DISTINCT 
                            TRIM(A.ITEM_CODE) AS ITEM_CODE, 
                            SUBSTR(TRIM(A.ITEM_NAME), 1, 100) AS ITEM_NAME, 
                            TRIM(A.BARCODE) AS BARCODE, 
                            NULL AS IMAGE, 
                            A.PRICE, 
                            SUBSTR(TRIM(A.DEPT_CODE), 1, 50) AS DEPT, 
                            :shop_code AS SHOP_CODE, 
                            0 AS CURR_STOCK, 
                            NVL(A.CH_PI, 'N') AS CH_PI, 
                            NVL(A.CH_STATUS, 'Y') AS CH_STATUS, 
                            SUBSTR(TRIM(A.GROUPS), 1, 50) AS VC_GROUP, 
                            SUBSTR(TRIM(A.SUB_GROUP), 1, 50) AS VC_SUBGROUP,
                            SUBSTR(TRIM(A.VC_UNIT), 1, 12) AS VC_UNIT,
                            A.ITEM_CODE AS VC_ITEM_CODE,
                            :shop_code AS VC_SHOP_CODE,
                            0 AS STOCK_QYT
                         FROM MAKESS.VS_ITEM_AUDIT@DB_LINK_SHOP A";

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
                            $group_filters[] = "(TRIM(A.GROUPS) = $gPlaceholder)";
                        } else {
                            // Subgroups are selected: filter by group AND subgroups IN list
                            $sub_placeholders = [];
                            foreach ($gSubgroups as $sCode) {
                                $sPlaceholder = ':sub_' . $bind_counter++;
                                $bind_params[$sPlaceholder] = $sCode;
                                $sub_placeholders[] = $sPlaceholder;
                            }
                            $group_filters[] = "(TRIM(A.GROUPS) = $gPlaceholder AND TRIM(A.SUB_GROUP) IN (" . implode(', ', $sub_placeholders) . "))";
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

        // Oracle PL/SQL Block executing dynamic explicit cursor transaction
        $plsqlQuery = "
            DECLARE
                CURSOR c_items IS $select_query;
            BEGIN
                FOR r IN c_items LOOP
                    INSERT INTO SHOP.MASTER_ITEM (
                        ITEM_CODE, ITEM_NAME, BARCODE, IMAGE, PRICE, DEPT, SHOP_CODE, CURR_STOCK, CH_PI, CH_STATUS, VC_GROUP, VC_SUBGROUP, VC_UNIT, VC_ITEM_CODE, VC_SHOP_CODE, STOCK_QYT
                    ) VALUES (
                        r.ITEM_CODE, r.ITEM_NAME, r.BARCODE, r.IMAGE, r.PRICE, r.DEPT, r.SHOP_CODE, r.CURR_STOCK, r.CH_PI, r.CH_STATUS, r.VC_GROUP, r.VC_SUBGROUP, r.VC_UNIT, r.VC_ITEM_CODE, r.VC_SHOP_CODE, r.STOCK_QYT
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

        $bulkExec = @oci_execute($bulkStmt);
        if (!$bulkExec) {
            $e = oci_error($bulkStmt);
            if ($bulkStmt) oci_free_statement($bulkStmt);
            throw new Exception("Cursor-based transactional insertion failed: " . $e['message']);
        }
        if ($bulkStmt) oci_free_statement($bulkStmt);

        // 5. Insert active setup parameters configuration row in SHOP.AUDIT_SETUP
        $insertQuery = "
            INSERT INTO SHOP.AUDIT_SETUP (
                STOCK_DATE, SHOP_CODE, AUDIT_TYPE, AUDIT_MODE, SELECTED_DEPTS, SELECTED_GROUPS, SELECTED_SUBGROUPS, MAIL
            ) VALUES (
                TO_DATE(:stock_date, 'YYYY-MM-DD'), :shop_code_param, :audit_type, :audit_mode, :depts, :groups, :subgroups, :mail
            )
        ";

        $insertStmt = oci_parse($conn, $insertQuery);
        oci_bind_by_name($insertStmt, ':stock_date', $stock_date_raw);
        oci_bind_by_name($insertStmt, ':shop_code_param', $shop_code);
        oci_bind_by_name($insertStmt, ':audit_type', $audit_type);
        oci_bind_by_name($insertStmt, ':audit_mode', $audit_mode);
        oci_bind_by_name($insertStmt, ':mail', $mail);
        oci_bind_by_name($insertStmt, ':depts', $depts);
        oci_bind_by_name($insertStmt, ':groups', $groups);
        oci_bind_by_name($insertStmt, ':subgroups', $subgroups);

        $executeResult = @oci_execute($insertStmt);
        if (!$executeResult) {
            $e = oci_error($insertStmt);
            oci_free_statement($insertStmt);
            throw new Exception("Setup parameters insertion failed: " . $e['message']);
        }
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
                    COUNT(DISTINCT TRIM(ITEM_CODE)) AS NO_OF_ITEMS,
                    SUM(NVL(TO_NUMBER(STOCK_QYT), 0)) AS TOTAL_QTY,
                    SUM(NVL(PRICE, 0)) AS TOTAL_VALUE 
                  FROM SHOP.MASTER_ITEM";
        $stmt = oci_parse($conn, $query);

        $no_of_items = 0;
        $total_qty = 0;
        $total_value = 0.0;
        if ($stmt && @oci_execute($stmt)) {
            if ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                $no_of_items = isset($row['NO_OF_ITEMS']) ? (int)$row['NO_OF_ITEMS'] : 0;
                $total_qty = isset($row['TOTAL_QTY']) ? (float)$row['TOTAL_QTY'] : 0;
                $total_value = isset($row['TOTAL_VALUE']) ? (float)$row['TOTAL_VALUE'] : 0.0;
            }
            oci_free_statement($stmt);
        }
        return [
            'status' => 'ok',
            'no_of_items' => $no_of_items,
            'total_qty' => $total_qty,
            'total_value' => $total_value
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

                        if (!isset($tree[$dept_code]['groups'][$group_desc])) {
                            $tree[$dept_code]['groups'][$group_desc] = [
                                'id' => $group_desc,
                                'name' => $group_desc,
                                'subgroups' => []
                            ];
                        }

                        $sub_exists = false;
                        foreach ($tree[$dept_code]['groups'][$group_desc]['subgroups'] as $existing_sub) {
                            if ($existing_sub['id'] === $sub_desc) {
                                $sub_exists = true;
                                break;
                            }
                        }
                        if (!$sub_exists) {
                            $tree[$dept_code]['groups'][$group_desc]['subgroups'][] = [
                                'id' => $sub_desc,
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
                            NULL AS IMAGE,
                            A.PRICE, 
                            SUBSTR(TRIM(A.DEPT_CODE), 1, 50) AS DEPT, 
                            :shop_code AS SHOP_CODE, 
                            0 AS CURR_STOCK, 
                            NVL(A.CH_PI, 'N') AS CH_PI, 
                            NVL(A.CH_STATUS, 'Y') AS CH_STATUS, 
                            SUBSTR(TRIM(A.GROUPS), 1, 50) AS VC_GROUP, 
                            SUBSTR(TRIM(A.SUB_GROUP), 1, 50) AS VC_SUBGROUP,
                            SUBSTR(TRIM(A.VC_UNIT), 1, 12) AS VC_UNIT,
                            A.ITEM_CODE AS VC_ITEM_CODE,
                            :shop_code AS VC_SHOP_CODE,
                            0 AS STOCK_QYT
                          FROM MAKESS.VS_ITEM_AUDIT@DB_LINK_SHOP A";

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
                            $group_filters[] = "(TRIM(A.GROUPS) = $gPlaceholder)";
                        } else {
                            // Subgroups are selected: filter by group AND subgroups IN list
                            $sub_placeholders = [];
                            foreach ($gSubgroups as $sCode) {
                                $sPlaceholder = ':sub_' . $bind_counter++;
                                $bind_params[$sPlaceholder] = $sCode;
                                $sub_placeholders[] = $sPlaceholder;
                            }
                            $group_filters[] = "(TRIM(A.GROUPS) = $gPlaceholder AND TRIM(A.SUB_GROUP) IN (" . implode(', ', $sub_placeholders) . "))";
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
                'IMAGE' => isset($row['IMAGE']) ? trim($row['IMAGE']) : 'N/A',
                'PRICE' => $row['PRICE'] !== null ? floatval($row['PRICE']) : 0.00,
                'DEPT' => isset($row['DEPT']) ? trim($row['DEPT']) : 'N/A',
                'SHOP_CODE' => isset($row['SHOP_CODE']) ? trim($row['SHOP_CODE']) : 'N/A',
                'CURR_STOCK' => isset($row['CURR_STOCK']) ? floatval($row['CURR_STOCK']) : 0.0,
                'CH_PI' => isset($row['CH_PI']) ? trim($row['CH_PI']) : 'N',
                'CH_STATUS' => isset($row['CH_STATUS']) ? trim($row['CH_STATUS']) : 'Y',
                'VC_GROUP' => isset($row['VC_GROUP']) ? trim($row['VC_GROUP']) : 'N/A',
                'VC_SUBGROUP' => isset($row['VC_SUBGROUP']) ? trim($row['VC_SUBGROUP']) : 'N/A',
                'VC_UNIT' => isset($row['VC_UNIT']) ? trim($row['VC_UNIT']) : 'N/A',
                'VC_ITEM_CODE' => isset($row['VC_ITEM_CODE']) ? trim($row['VC_ITEM_CODE']) : 'N/A',
                'VC_SHOP_CODE' => isset($row['VC_SHOP_CODE']) ? trim($row['VC_SHOP_CODE']) : 'N/A',
                'STOCK_QYT' => isset($row['STOCK_QYT']) ? floatval($row['STOCK_QYT']) : 0.0
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
    }
}
?>
