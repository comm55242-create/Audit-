<?php
$depts = '300, 301';
$groups = '300|G1, 301|G2';
$subgroups = '';

$dept_list = array_filter(array_map('trim', explode(',', $depts)));
$group_list = array_filter(array_map('trim', explode(',', $groups)));
$subgroup_list = array_filter(array_map('trim', explode(',', $subgroups)));

$where_clauses = [];
$bind_params = [];
$bind_counter = 0;

$dept_filters = [];
foreach ($dept_list as $dCode) {
    $dGroups = [];
    foreach ($group_list as $gStr) {
        $gParts = explode('|', $gStr);
        if (count($gParts) === 2 && $gParts[0] === $dCode) {
            $dGroups[] = $gParts[1];
        }
    }

    if (empty($dGroups)) {
        $pName = ':dept_' . $bind_counter++;
        $bind_params[$pName] = $dCode;
        $dept_filters[] = '(TRIM(A.DEPT_CODE) = ' . $pName . ')';
    } else {
        $group_filters = [];
        foreach ($dGroups as $gCode) {
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
                $group_filters[] = '(TRIM(A.VC_GROUP_CODE) = ' . $gPlaceholder . ')';
            } else {
                $sub_placeholders = [];
                foreach ($gSubgroups as $sCode) {
                    $sPlaceholder = ':sub_' . $bind_counter++;
                    $bind_params[$sPlaceholder] = $sCode;
                    $sub_placeholders[] = $sPlaceholder;
                }
                $sub_chunks = array_chunk($sub_placeholders, 999);
                $chunk_queries = [];
                foreach ($sub_chunks as $chunk) {
                    $chunk_queries[] = 'TRIM(A.VC_SUB_GROUP_CODE) IN (' . implode(', ', $chunk) . ')';
                }
                $group_filters[] = '(TRIM(A.VC_GROUP_CODE) = ' . $gPlaceholder . ' AND (' . implode(' OR ', $chunk_queries) . '))';
            }
        }

        $dPlaceholder = ':dept_' . $bind_counter++;
        $bind_params[$dPlaceholder] = $dCode;
        $dept_filters[] = '(TRIM(A.DEPT_CODE) = ' . $dPlaceholder . ' AND (' . implode(' OR ', $group_filters) . '))';
    }
}
if (!empty($dept_filters)) {
    $where_clauses[] = '(' . implode(' OR ', $dept_filters) . ')';
}
echo "WHERE CLAUSE: \n";
echo '(' . implode(' OR ', $dept_filters) . ')\n';
echo "\nBIND PARAMS: \n";
print_r($bind_params);
