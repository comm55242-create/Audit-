<?php
$c = file_get_contents('export.php');
$c = preg_replace('/^\s*fputcsv\(\$fp,\s*\$data\);/m', '        if(!isset($seen[md5(serialize($data))])){ $seen[md5(serialize($data))] = true; fputcsv($fp, $data); }', $c);
$c = preg_replace('/^\s*fputcsv\(\$fp,\s*\$row\);/m', '        if(!isset($seen[md5(serialize($row))])){ $seen[md5(serialize($row))] = true; fputcsv($fp, $row); }', $c);
$c = preg_replace('/<\?php/', "<?php\n\$seen = [];", $c, 1);
file_put_contents('export.php', $c);
echo "Done.";
