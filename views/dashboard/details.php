<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - DETAILS WORKSPACE VIEW
// Premium digital spreadsheets, Drag-and-Drop secure CSV parsers, and sheets.
// ==========================================================================
?>

<div class="details-grid">

    <!-- CARD 1: STOCK TAKE INFORMATION SHEET -->
    <div class="details-card">
        <div class="details-card-header" onclick="toggleDetailsCard('secStockInfo')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <span>1. Stock Take Information Sheet</span>
            </span>
            <span id="icon-secStockInfo" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ COLLAPSE ]</span>
        </div>
        
        <div id="body-secStockInfo" class="details-card-body">
            <!-- 2x2 Grid of visual panels (i. ii. iii. iv.) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
                
                <!-- Group I: Store Identification & Period -->
                <div style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                    <h4 style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: var(--color-primary); margin-bottom: 0.25rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: var(--color-primary-light); color: var(--color-primary); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900;">I</span>
                        <span>Store & Audit Timeline</span>
                    </h4>
                    <div class="form-field">
                        <label class="form-label">Store Name</label>
                        <input type="text" id="infoStoreName" class="form-input" placeholder="e.g. Accra Central Mall" oninput="saveStockInfoState()">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field">
                            <label class="form-label">Store Code</label>
                            <input type="text" id="infoStoreCode" class="form-input" placeholder="e.g. ACC-01" oninput="saveStockInfoState()">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Audit Period</label>
                            <input type="text" id="infoAuditPeriod" class="form-input" placeholder="e.g. Q2 2026" oninput="saveStockInfoState()">
                        </div>
                    </div>
                </div>

                <!-- Group II: Store & Operations Contacts -->
                <div style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                    <h4 style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: var(--color-primary); margin-bottom: 0.25rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: var(--color-primary-light); color: var(--color-primary); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900;">II</span>
                        <span>Store & Operations Management</span>
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field">
                            <label class="form-label">Store Manager Name</label>
                            <input type="text" id="infoStoreManager" class="form-input" placeholder="e.g. Samuel Kojo" oninput="saveStockInfoState()">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Manager Contact</label>
                            <input type="tel" id="infoStoreManagerContact" class="form-input" placeholder="e.g. +233 24 123 4567" oninput="saveStockInfoState()">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field">
                            <label class="form-label">Operations Manager</label>
                            <input type="text" id="infoOpsManager" class="form-input" placeholder="e.g. David Mensah" oninput="saveStockInfoState()">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Ops Contact</label>
                            <input type="tel" id="infoOpsManagerContact" class="form-input" placeholder="e.g. +233 50 987 6543" oninput="saveStockInfoState()">
                        </div>
                    </div>
                </div>

                <!-- Group III: Audit Leadership & Roster Counts -->
                <div style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                    <h4 style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: var(--color-primary); margin-bottom: 0.25rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: var(--color-primary-light); color: var(--color-primary); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900;">III</span>
                        <span>Audit Personnel & Counts</span>
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field">
                            <label class="form-label">Audit Lead</label>
                            <input type="text" id="infoAuditLead" class="form-input" placeholder="e.g. David Ocloo" oninput="saveStockInfoState()">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Auditor (HO)</label>
                            <input type="text" id="infoAuditorHO" class="form-input" placeholder="e.g. Head Office Inspector" oninput="saveStockInfoState()">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field">
                            <label class="form-label">Audit Team Count</label>
                            <input type="number" id="infoAuditTeamCount" class="form-input" min="0" placeholder="e.g. 5" oninput="saveStockInfoState()">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Ops Team Count</label>
                            <input type="number" id="infoOpsTeamCount" class="form-input" min="0" placeholder="e.g. 12" oninput="saveStockInfoState()">
                        </div>
                    </div>
                </div>

                <!-- Group IV: Zone Details -->
                <div style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                    <h4 style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: var(--color-primary); margin-bottom: 0.25rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: var(--color-primary-light); color: var(--color-primary); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900;">IV</span>
                        <span>Zone Details & Notes</span>
                    </h4>
                    <div class="form-field" style="flex: 1; display: flex; flex-direction: column;">
                        <label class="form-label">Zone Details</label>
                        <textarea id="infoZoneDetails" class="form-input" style="flex: 1; min-height: 104px; resize: vertical; padding: 0.75rem; font-size: 13px;" placeholder="Describe active audit zones, department codes, scanner mappings, or specific boundaries..." oninput="saveStockInfoState()"></textarea>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- CARD 2: AUDIT STAFF ATTENDANCE SHEET -->
    <div class="details-card">
        <div class="details-card-header" onclick="toggleDetailsCard('secAttendance')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
                <span>2. Audit Staff Attendance Sheet</span>
            </span>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span id="badge-secAttendance" class="badge badge-warning">Empty</span>
                <span id="icon-secAttendance" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ COLLAPSE ]</span>
            </div>
        </div>
        
        <div id="body-secAttendance" class="details-card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="font-size: 12px; color: var(--color-text-muted);">Upload the parsed csv personnel data to audit attendance registry.</span>
                <a href="javascript:void(0)" onclick="downloadCsvTemplate('attendance')" class="template-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;stroke-width:2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download Attendance Template</span>
                </a>
            </div>

            <!-- Upload Area -->
            <div id="uploadAttendance" class="upload-zone" onclick="triggerFileInput('fileAttendance')" ondragover="handleDragOver(event, this)" ondragleave="handleDragLeave(event, this)" ondrop="handleDrop(event, this, 'attendance')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="upload-zone-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span class="upload-zone-text">Drag and drop your attendance CSV file here, or <span style="color: var(--color-primary); text-decoration: underline;">browse files</span></span>
                <span class="upload-zone-subtext">Supports CSV format with Staff Name, Staff ID, Department, Shift, Check-in Time headers</span>
                <input type="file" id="fileAttendance" accept=".csv" style="display: none;" onchange="handleFileSelect(event, 'attendance')">
            </div>

            <!-- Parsed Results Table -->
            <div id="wrapperAttendance" class="table-wrapper hidden" style="margin-top: 1.25rem;">
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Staff ID</th>
                            <th>Department</th>
                            <th>Shift</th>
                            <th>Check-in Time</th>
                        </tr>
                    </thead>
                    <tbody id="tableAttendanceBody">
                        <!-- CSV rows rendered dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CARD 3: ZONE TRACKER -->
    <div class="details-card">
        <div class="details-card-header" onclick="toggleDetailsCard('secZones')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <span>3. Zone Tracker</span>
            </span>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span id="badge-secZones" class="badge badge-warning">Empty</span>
                <span id="icon-secZones" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ COLLAPSE ]</span>
            </div>
        </div>
        
        <div id="body-secZones" class="details-card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="font-size: 12px; color: var(--color-text-muted);">Sync the physical store zones list to keep tracking scanning progress.</span>
                <a href="javascript:void(0)" onclick="downloadCsvTemplate('zones')" class="template-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;stroke-width:2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download Zone Template</span>
                </a>
            </div>

            <!-- Upload Area -->
            <div id="uploadZones" class="upload-zone" onclick="triggerFileInput('fileZones')" ondragover="handleDragOver(event, this)" ondragleave="handleDragLeave(event, this)" ondrop="handleDrop(event, this, 'zones')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="upload-zone-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="upload-zone-text">Drag and drop your Zone CSV file here, or <span style="color: var(--color-primary); text-decoration: underline;">browse files</span></span>
                <span class="upload-zone-subtext">Supports CSV format with Zone ID, Zone Description, Scanned Count, Status headers</span>
                <input type="file" id="fileZones" accept=".csv" style="display: none;" onchange="handleFileSelect(event, 'zones')">
            </div>

            <!-- Parsed Results Table -->
            <div id="wrapperZones" class="table-wrapper hidden" style="margin-top: 1.25rem;">
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Zone ID</th>
                            <th>Zone Description</th>
                            <th>Scanned Items</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableZonesBody">
                        <!-- CSV rows rendered dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CARD 4: SCAN CONTROL SHEET -->
    <div class="details-card">
        <div class="details-card-header" onclick="toggleDetailsCard('secScanners')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </span>
                <span>4. Scan Control Sheet</span>
            </span>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span id="badge-secScanners" class="badge badge-warning">Empty</span>
                <span id="icon-secScanners" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ COLLAPSE ]</span>
            </div>
        </div>
        
        <div id="body-secScanners" class="details-card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="font-size: 12px; color: var(--color-text-muted);">Manage and monitor terminal barcode scanning devices assigned to audit staff.</span>
                <a href="javascript:void(0)" onclick="downloadCsvTemplate('scanners')" class="template-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;stroke-width:2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download Scan Control Template</span>
                </a>
            </div>

            <!-- Upload Area -->
            <div id="uploadScanners" class="upload-zone" onclick="triggerFileInput('fileScanners')" ondragover="handleDragOver(event, this)" ondragleave="handleDragLeave(event, this)" ondrop="handleDrop(event, this, 'scanners')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="upload-zone-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                <span class="upload-zone-text">Drag and drop your Scan Control CSV file here, or <span style="color: var(--color-primary); text-decoration: underline;">browse files</span></span>
                <span class="upload-zone-subtext">Supports CSV format with Scanner ID, Assigned User, Total Scanned, Verification Status headers</span>
                <input type="file" id="fileScanners" accept=".csv" style="display: none;" onchange="handleFileSelect(event, 'scanners')">
            </div>

            <!-- Parsed Results Table -->
            <div id="wrapperScanners" class="table-wrapper hidden" style="margin-top: 1.25rem;">
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Scanner ID</th>
                            <th>Assigned Auditor</th>
                            <th>Total Items Scanned</th>
                            <th>Verification Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableScannersBody">
                        <!-- CSV rows rendered dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- HIGH FIDELITY CSV PARSING SCRIPTS -->
<!-- ========================================== -->
<script>
    /**
     * Toggles Collapsible details sections.
     */
    function toggleDetailsCard(cardId) {
        const body = document.getElementById("body-" + cardId);
        const icon = document.getElementById("icon-" + cardId);
        if (body.classList.contains("hidden")) {
            body.classList.remove("hidden");
            icon.innerText = "[ COLLAPSE ]";
        } else {
            body.classList.add("hidden");
            icon.innerText = "[ EXPAND ]";
        }
    }

    /**
     * Redirects click to hidden file inputs.
     */
    function triggerFileInput(inputId) {
        document.getElementById(inputId).click();
    }

    /**
     * Drag-and-drop Visual Highlighters.
     */
    function handleDragOver(e, el) {
        e.preventDefault();
        el.style.borderColor = "var(--color-primary)";
        el.style.backgroundColor = "var(--color-primary-light)";
    }

    function handleDragLeave(e, el) {
        e.preventDefault();
        el.style.borderColor = "#cbd5e1";
        el.style.backgroundColor = "#f8fafc";
    }

    /**
     * Drag-and-drop Drop Interceptor.
     */
    function handleDrop(e, el, type) {
        e.preventDefault();
        el.style.borderColor = "#cbd5e1";
        el.style.backgroundColor = "#f8fafc";
        
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            parseCsvFile(e.dataTransfer.files[0], type);
        }
    }

    function handleFileSelect(e, type) {
        if (e.target.files && e.target.files.length > 0) {
            parseCsvFile(e.target.files[0], type);
        }
    }

    /**
     * Reads and Parses CSV in browser using FileReader.
     */
    function parseCsvFile(file, type) {
        if (!file.name.endsWith(".csv")) {
            alert("Format Error: Please upload a valid CSV file!");
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            const text = event.target.result;
            const rows = parseCsvText(text);
            
            if (rows.length < 2) {
                alert("Data Error: The uploaded CSV file has no records!");
                return;
            }

            renderParsedData(rows, type);
            saveDetailsState(type, text);
        };
        reader.readAsText(file);
    }

    /**
     * Parses CSV text robustly (handling double quotes and escapes).
     */
    function parseCsvText(text) {
        const lines = [];
        let row = [""];
        let inQuotes = false;

        for (let i = 0; i < text.length; i++) {
            const char = text[i];
            const next = text[i+1];

            if (char === '"') {
                if (inQuotes && next === '"') {
                    row[row.length - 1] += '"';
                    i++;
                } else {
                    inQuotes = !inQuotes;
                }
            } else if (char === ',' && !inQuotes) {
                row.push("");
            } else if ((char === '\r' || char === '\n') && !inQuotes) {
                if (char === '\r' && next === '\n') {
                    i++;
                }
                lines.push(row);
                row = [""];
            } else {
                row[row.length - 1] += char;
            }
        }
        if (row.length > 1 || row[0] !== "") {
            lines.push(row);
        }
        return lines.filter(l => l.some(cell => cell.trim() !== ""));
    }

    /**
     * Renders Parsed Data into styled sheets.
     */
    function renderParsedData(rows, type) {
        const headers = rows[0].map(h => h.trim().toUpperCase());
        const dataRows = rows.slice(1);
        
        let html = "";
        let count = dataRows.length;

        if (type === 'attendance') {
            const tbody = document.getElementById("tableAttendanceBody");
            dataRows.forEach(row => {
                html += `<tr>
                    <td style="font-weight: 800; color: var(--color-text-main);">${escapeHtml(row[0] || 'N/A')}</td>
                    <td style="font-family: monospace; font-weight: 700;">${escapeHtml(row[1] || 'N/A')}</td>
                    <td>${escapeHtml(row[2] || 'General')}</td>
                    <td><span class="badge ${row[3]?.trim().toLowerCase() === 'night' ? 'badge-info' : 'badge-success'}">${escapeHtml(row[3] || 'Day')}</span></td>
                    <td style="font-weight: 700; color: var(--color-primary);">${escapeHtml(row[4] || '--:--')}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
            document.getElementById("wrapperAttendance").classList.remove("hidden");
            updateBadge('secAttendance', `${count} Active`, 'success');
            
        } else if (type === 'zones') {
            const tbody = document.getElementById("tableZonesBody");
            dataRows.forEach(row => {
                const status = (row[3] || 'Pending').trim().toLowerCase();
                const badgeClass = status === 'scanned' ? 'badge-success' : 'badge-warning';
                
                html += `<tr>
                    <td style="font-family: monospace; font-weight: 800; color: var(--color-text-main);">${escapeHtml(row[0] || 'N/A')}</td>
                    <td>${escapeHtml(row[1] || 'N/A')}</td>
                    <td style="font-weight: 800; font-family: monospace;">${parseInt(row[2] || '0').toLocaleString()}</td>
                    <td><span class="badge ${badgeClass}">${escapeHtml(row[3] || 'Pending')}</span></td>
                </tr>`;
            });
            tbody.innerHTML = html;
            document.getElementById("wrapperZones").classList.remove("hidden");
            updateBadge('secZones', `${count} Zones`, 'success');
            
        } else if (type === 'scanners') {
            const tbody = document.getElementById("tableScannersBody");
            dataRows.forEach(row => {
                const status = (row[3] || 'Pending').trim().toLowerCase();
                const isVerified = status === 'verified' || status === 'completed';
                
                html += `<tr>
                    <td style="font-family: monospace; font-weight: 800; color: var(--color-text-main);">${escapeHtml(row[0] || 'N/A')}</td>
                    <td style="font-weight: 700;">${escapeHtml(row[1] || 'Unassigned')}</td>
                    <td style="font-weight: 800; font-family: monospace;">${parseInt(row[2] || '0').toLocaleString()}</td>
                    <td><span class="badge ${isVerified ? 'badge-success' : 'badge-warning'}">${escapeHtml(row[3] || 'Pending')}</span></td>
                </tr>`;
            });
            tbody.innerHTML = html;
            document.getElementById("wrapperScanners").classList.remove("hidden");
            updateBadge('secScanners', `${count} Devices`, 'success');
        }

        updateSidebarDetailsTracker();
    }

    function updateBadge(secId, text, type) {
        const badge = document.getElementById("badge-" + secId);
        if (badge) {
            badge.innerText = text;
            badge.className = "badge badge-" + type;
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    /**
     * Generates and triggers CSV Template download dynamically.
     */
    function downloadCsvTemplate(type) {
        let csvContent = "";
        let filename = "";

        if (type === 'attendance') {
            csvContent = "Staff Name,Staff ID,Department,Shift,Check-in Time\nDavid Ocloo,M104,IT Support,Day,08:15 AM\nSamuel Amegbletor,M203,Internal Audit,Day,08:30 AM\nAuditor Kojo,M405,Inventory,Night,10:00 PM\nOfficer Zahed,M301,Management,Day,09:00 AM";
            filename = "attendance_template.csv";
        } else if (type === 'zones') {
            csvContent = "Zone ID,Zone Description,Scanned Count,Status\nZONE-01,Grocery Aisle 1 (A-C),1420,Scanned\nZONE-02,Electronics Wall Case,580,Scanned\nZONE-03,Warehouse Cold Room,0,Pending\nZONE-04,Main Counter Display,120,Scanned\nZONE-05,Souk Vegetable Baskets,420,Scanned";
            filename = "zones_template.csv";
        } else if (type === 'scanners') {
            csvContent = "Scanner ID,Assigned User,Total Scanned,Verification Status\nSCN-801,David Ocloo,1420,Verified\nSCN-802,Samuel,580,Verified\nSCN-803,Auditor Kojo,0,Pending\nSCN-804,Officer Zahed,540,Verified";
            filename = "scanners_template.csv";
        }

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

    // -------------------------------------------------------------
    // LOCAL STORAGE STATE STATE PERSISTENCE
    // -------------------------------------------------------------
    function saveStockInfoState() {
        const state = {
            storeName: document.getElementById("infoStoreName").value,
            storeCode: document.getElementById("infoStoreCode").value,
            storeManager: document.getElementById("infoStoreManager").value,
            storeManagerContact: document.getElementById("infoStoreManagerContact").value,
            opsManager: document.getElementById("infoOpsManager").value,
            opsManagerContact: document.getElementById("infoOpsManagerContact").value,
            auditLead: document.getElementById("infoAuditLead").value,
            auditorHO: document.getElementById("infoAuditorHO").value,
            auditTeamCount: document.getElementById("infoAuditTeamCount").value,
            opsTeamCount: document.getElementById("infoOpsTeamCount").value,
            auditPeriod: document.getElementById("infoAuditPeriod").value,
            zoneDetails: document.getElementById("infoZoneDetails").value
        };
        localStorage.setItem("melcom_details_info", JSON.stringify(state));
        updateSidebarDetailsTracker();
    }

    function saveDetailsState(type, csvText) {
        localStorage.setItem("melcom_details_csv_" + type, csvText);
    }

    function loadDetailsState() {
        // 1. Load Stock Info
        const info = localStorage.getItem("melcom_details_info");
        if (info) {
            try {
                const state = JSON.parse(info);
                document.getElementById("infoStoreName").value = state.storeName || "";
                document.getElementById("infoStoreCode").value = state.storeCode || "";
                document.getElementById("infoStoreManager").value = state.storeManager || "";
                document.getElementById("infoStoreManagerContact").value = state.storeManagerContact || "";
                document.getElementById("infoOpsManager").value = state.opsManager || "";
                document.getElementById("infoOpsManagerContact").value = state.opsManagerContact || "";
                document.getElementById("infoAuditLead").value = state.auditLead || "";
                document.getElementById("infoAuditorHO").value = state.auditorHO || "";
                document.getElementById("infoAuditTeamCount").value = state.auditTeamCount || "";
                document.getElementById("infoOpsTeamCount").value = state.opsTeamCount || "";
                document.getElementById("infoAuditPeriod").value = state.auditPeriod || "";
                document.getElementById("infoZoneDetails").value = state.zoneDetails || "";
            } catch(e) {}
        }

        // 2. Load CSV Tables
        ['attendance', 'zones', 'scanners'].forEach(type => {
            const csv = localStorage.getItem("melcom_details_csv_" + type);
            if (csv) {
                const rows = parseCsvText(csv);
                if (rows && rows.length > 0) {
                    renderParsedData(rows, type);
                }
            }
        });
        
        updateSidebarDetailsTracker();
    }

    function updateSidebarDetailsTracker() {
        // Check if there is an active progress tracker panel in sidebar
        const tracker = document.getElementById("sidebarDetailsProgressText");
        const fill = document.getElementById("sidebarDetailsProgress");
        if (!tracker) return;

        let completed = 0;
        
        // Check stock take info (critical fields)
        const storeName = document.getElementById("infoStoreName").value.trim();
        const auditLead = document.getElementById("infoAuditLead").value.trim();
        if (storeName !== "" && auditLead !== "") completed++;

        // Check CSVs
        if (localStorage.getItem("melcom_details_csv_attendance")) completed++;
        if (localStorage.getItem("melcom_details_csv_zones")) completed++;
        if (localStorage.getItem("melcom_details_csv_scanners")) completed++;

        const percent = Math.round((completed / 4) * 100);
        tracker.innerText = `${completed}/4 Completed`;
        if (fill) fill.style.width = `${percent}%`;
    }

    // Auto-run state loaders on dom content load
    window.addEventListener("DOMContentLoaded", () => {
        loadDetailsState();
    });
</script>
