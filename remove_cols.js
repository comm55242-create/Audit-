const fs = require('fs');
let f = fs.readFileSync('views/dashboard/details.php', 'utf8');

// Remove table headers
f = f.replace(/<th.*?Status<br><small>\(Store Manager\)<\/small><\/th>\s*<th.*?Remarks<br><small>\(Store Manager\)<\/small><\/th>/g, '');

// Remove table cells
f = f.replace(/<td><select id="smStatus-\d+".*?<\/select><\/td>\s*<td><input type="text" id="smRemark-\d+".*?><\/td>/g, '');

// Fix colspan in the subheaders to match 4 columns instead of 6
f = f.replace(/<td colspan="6"/g, '<td colspan="4"');

fs.writeFileSync('views/dashboard/details.php', f);
console.log('Done');
