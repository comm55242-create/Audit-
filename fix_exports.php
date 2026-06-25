<?php
$file = 'c:\\Users\\USER\\Workspaces\\htdocs\\Melcom Mobile\\AUDIT_OLD\\postfolder\\export.php';
$content = file_get_contents($file);

if (strpos($content, '$seen = [];') === false) {
    $content = preg_replace('/<\?php\s+/', "<?php \n\$seen = [];\n", $content, 1);
}

$lines = explode("\n", $content);
foreach ($lines as &$line) {
    if (preg_match('/fputcsv\(\s*\$fp\s*,\s*(\$[a-zA-Z0-9_]+)\s*\);/', $line, $matches)) {
        if (strpos($line, '$seen') === false) {
            $var = $matches[1];
            $replace = "if(!isset(\$seen[md5(serialize($var))])){ \$seen[md5(serialize($var))] = true; fputcsv(\$fp, $var); }";
            $line = preg_replace('/fputcsv\(\s*\$fp\s*,\s*\$[a-zA-Z0-9_]+\s*\);/', $replace, $line);
        }
    }
}
$newContent = implode("\n", $lines);
file_put_contents($file, $newContent);
echo "Done replacing.\n";
?>
