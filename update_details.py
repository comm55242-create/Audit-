import re

with open('views/dashboard/details.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove Table Headers for Store Manager
content = re.sub(r'<th[^>]*>Status<br><small>\(Store Manager\)</small></th>', '', content)
content = re.sub(r'<th[^>]*>Remarks<br><small>\(Store Manager\)</small></th>', '', content)

# 2. Remove Table Data cells for smStatus and smRemark
content = re.sub(r'<td><select id="smStatus-\d+".*?</select></td>', '', content)
content = re.sub(r'<td><input type="text" id="smRemark-\d+".*?></td>', '', content)

# 3. Change colspan="6" to colspan="4"
content = content.replace('colspan="6"', 'colspan="4"')

# 4. Update JS saveChecklistState
content = re.sub(r'const smStatus = document\.getElementById\("smStatus-" \+ i\)\.value;', '', content)
content = re.sub(r'const smRemark = document\.getElementById\("smRemark-" \+ i\)\.value;', '', content)
content = content.replace('preStockState.push({ smStatus, smRemark, auStatus, auRemark });', 'preStockState.push({ auStatus, auRemark });')

content = re.sub(r'const smSel = document\.getElementById\("smStatus-" \+ i\);\s*if \(smStatus === \'Yes\'\) \{\s*smSel\.style\.backgroundColor = \'#dcfce7\';\s*smSel\.style\.color = \'#166534\';\s*\} else if \(smStatus === \'No\'\) \{\s*smSel\.style\.backgroundColor = \'#fee2e2\';\s*smSel\.style\.color = \'#991b1b\';\s*\} else \{\s*smSel\.style\.backgroundColor = \'#f8fafc\';\s*smSel\.style\.color = \'#334155\';\s*\}', '', content)

# 5. Update validation
content = content.replace('if (document.getElementById("smStatus-" + i).value !== \'\' && document.getElementById("auStatus-" + i).value !== \'\') {', 'if (document.getElementById("auStatus-" + i).value !== \'\') {')

# 6. Update restoreChecklistState
content = re.sub(r'if \(document\.getElementById\("smStatus-" \+ i\)\) \{\s*document\.getElementById\("smStatus-" \+ i\)\.value = item\.smStatus \|\| "";\s*\}', '', content)
content = re.sub(r'if \(document\.getElementById\("smRemark-" \+ i\)\) \{\s*document\.getElementById\("smRemark-" \+ i\)\.value = item\.smRemark \|\| "";\s*\}', '', content)

# 7. Update evaluatePreStockProgress
content = content.replace('if (document.getElementById("smStatus-" + i) && document.getElementById("smStatus-" + i).value !== \'\' && document.getElementById("auStatus-" + i) && document.getElementById("auStatus-" + i).value !== \'\') completedPre++;', 'if (document.getElementById("auStatus-" + i) && document.getElementById("auStatus-" + i).value !== \'\') completedPre++;')

# 8. Update generatePrintableSignOff
content = re.sub(r'const smStat = document\.getElementById\("smStatus-" \+ i\)\?\.value \|\| "-";', '', content)
content = re.sub(r'const smRem = document\.getElementById\("smRemark-" \+ i\)\?\.value \|\| "";', '', content)

content = re.sub(r'<td style="text-align: center; font-weight: 600; color: \$\{smStat === \'Yes\' \? \'#16a34a\' : \(smStat === \'No\' \? \'#dc2626\' : \'#64748b\'\)\};">\$\{smStat\}</td>', '', content)
content = content.replace('<td>${smRem}</td>', '')

# Also remove from print headers
content = re.sub(r'<th style="width: 80px; text-align: center; border: 1px solid #cbd5e1; padding: 10px;">Status<br>\(Store Mgr\)</th>', '', content)
content = re.sub(r'<th style="width: 150px; text-align: left; border: 1px solid #cbd5e1; padding: 10px;">Remarks<br>\(Store Mgr\)</th>', '', content)

# Wait, add Audit Start/End dates to Print Checklist
printHtmlAdd = """
        <div style="margin-bottom: 20px; font-size: 13px;">
            <b>Audit Start Date:</b> ${document.getElementById('infoAuditStartDate')?.value || 'N/A'} <br>
            <b>Audit End Date:</b> ${document.getElementById('infoAuditEndDate')?.value || 'N/A'}
        </div>
"""
content = content.replace('<h2>Pre-Stock Take Checklist Workflows</h2>', '<h2>Pre-Stock Take Checklist Workflows</h2>' + printHtmlAdd)

with open('views/dashboard/details.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("details.php updated successfully!")
