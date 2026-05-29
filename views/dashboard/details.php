<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - NEW CONSOLIDATED DETAILS & CHECKLIST WORKSPACE VIEW
// Sequential wizard featuring:
// 1. Live submitted Stock Audit uneditable summary panel (with verification checkbox)
// 2. Step 1: Stock Take Information Sheet
// 3. Step 2: Merged Attendance & Zone Tracker CSV Parser (SI, STAFF NAME, ROLE IN AUDIT, ZONE NAME, SCANNING ID)
// 4. Step 3: Pre-Stock Take Checklist Workflow Board
// 5. Step 4: Mandatory Report Alert Checklist Board
// ==========================================================================
?>

<div class="details-grid" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%; max-width: 1152px; margin: 0 auto;">

    <!-- ========================================== -->
    <!-- UNEDITABLE STOCK AUDIT SETUP SUMMARY CARD -->
    <!-- ========================================== -->
    <div id="stockAuditSubmittedSummary" class="details-card hidden" style="background: var(--color-primary-light); border: 1px solid var(--color-primary-ring); padding: 1.5rem; border-radius: 18px; display: flex; flex-direction: column; gap: 1.25rem; width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
            <span style="font-size: 0.9rem; font-weight: 900; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.5; color: var(--color-primary);"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Active Stock Audit Setup Parameters (Uneditable)</span>
            </span>
            <span class="badge badge-success" style="font-size: 10px; font-weight: 800; text-transform: uppercase;">Submitted & Locked</span>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 13px;">
            <div>
                <span style="font-weight: 500; color: var(--color-text-muted);">Shop Code:</span>
                <strong id="sumShopCode" style="color: var(--color-text-main); font-family: monospace; font-size: 14px;">-</strong>
            </div>
            <div>
                <span style="font-weight: 500; color: var(--color-text-muted);">Stock Date:</span>
                <strong id="sumStockDate" style="color: var(--color-text-main); font-family: monospace; font-size: 14px;">-</strong>
            </div>
            <div>
                <span style="font-weight: 500; color: var(--color-text-muted);">Audit Type:</span>
                <strong id="sumAuditType" style="color: var(--color-text-main);">-</strong>
            </div>
            <div>
                <span style="font-weight: 500; color: var(--color-text-muted);">Audit Mode:</span>
                <strong id="sumAuditMode" style="color: var(--color-text-main);">-</strong>
            </div>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 0.35rem; font-size: 12px; background: #ffffff; border: 1px solid var(--color-border); border-radius: 12px; padding: 0.75rem 1rem;">
            <div>
                <span style="font-weight: 700; color: var(--color-text-muted);">Scope Departments:</span>
                <span id="sumDepts" style="color: var(--color-text-main); font-weight: 500;">None</span>
            </div>
            <div style="margin-top: 0.25rem; border-top: 1px solid var(--color-border); padding-top: 0.25rem;">
                <span style="font-weight: 700; color: var(--color-text-muted);">Scope Segments:</span>
                <span id="sumSegments" style="color: var(--color-text-main); font-weight: 500;">None</span>
            </div>
        </div>

        <label style="display: flex; align-items: center; gap: 0.75rem; background: #ffffff; border: 1px solid var(--color-border); border-radius: 12px; padding: 0.75rem 1rem; cursor: pointer; user-select: none;">
            <input type="checkbox" id="chkConfirmSetupVerified" style="width: 18px; height: 18px; accent-color: var(--color-primary); cursor: pointer;" onchange="saveDetailsVerificationCheckbox()">
            <span style="font-size: 13px; font-weight: 700; color: var(--color-text-main);">I confirm that I have reviewed, verified and approved this submitted Stock Audit Setup on the Details Sheet.</span>
        </label>
    </div>

    <!-- ========================================== -->
    <!-- STEP 1: STOCK TAKE INFORMATION SHEET -->
    <!-- ========================================== -->
    <div id="panelDetailsStep-1" class="details-card">
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
            <div style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">
                
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

    <!-- ========================================== -->
    <!-- STEP 2: MERGED ATTENDANCE / ZONE TRACKER CSV UPLOAD -->
    <!-- ========================================== -->
    <div id="panelDetailsStep-2" class="details-card hidden">
        <div class="details-card-header" onclick="toggleDetailsCard('secMerged')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
                <span>2. Attendance / Zone Tracker File Sync</span>
            </span>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span id="badge-secMerged" class="badge badge-warning">Empty</span>
                <span id="icon-secMerged" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ COLLAPSE ]</span>
            </div>
        </div>
        
        <div id="body-secMerged" class="details-card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="font-size: 12px; color: var(--color-text-muted);">Upload your combined auditor attendance roster and zone terminal devices CSV sheet.</span>
                <a href="javascript:void(0)" onclick="downloadCsvTemplate('merged')" class="template-link" style="display: flex; align-items: center; gap: 0.25rem; font-size: 12px; font-weight: 700; color: var(--color-primary); text-decoration: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;stroke-width:2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download Merged CSV Template</span>
                </a>
            </div>

            <!-- Upload Area -->
            <div id="uploadMerged" class="upload-zone" onclick="triggerFileInput('fileMerged')" ondragover="handleDragOver(event, this)" ondragleave="handleDragLeave(event, this)" ondrop="handleDrop(event, this, 'merged')" style="border: 2px dashed #cbd5e1; border-radius: 14px; padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem; background: #f8fafc; cursor: pointer; transition: all 0.2s ease;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="upload-zone-icon" style="width: 38px; height: 38px; color: var(--color-text-light);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span class="upload-zone-text" style="font-size: 13px; font-weight: 700; color: var(--color-text-main);">Drag and drop your combined Attendance/Zone CSV here, or <span style="color: var(--color-primary); text-decoration: underline;">browse files</span></span>
                <span class="upload-zone-subtext" style="font-size: 11px; color: var(--color-text-light);">Supports headers: SI, STAFF NAME, ROLE IN AUDIT, ZONE NAME, SCANNING ID</span>
                <input type="file" id="fileMerged" accept=".csv" style="display: none;" onchange="handleFileSelect(event, 'merged')">
            </div>

            <!-- Parsed Results Table -->
            <div id="wrapperMerged" class="table-wrapper hidden" style="margin-top: 1.25rem;">
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th style="width: 70px; text-align: center;">SI</th>
                            <th>Staff Name</th>
                            <th>Role in Audit</th>
                            <th>Zone Name</th>
                            <th>Scanning ID</th>
                        </tr>
                    </thead>
                    <tbody id="tableMergedBody">
                        <!-- CSV rows rendered dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STEP 3: PRE-STOCK TAKE CHECKLIST WORKFLOW -->
    <!-- ========================================== -->
    <div id="panelDetailsStep-3" class="details-card hidden">
        <div class="details-card-header" onclick="toggleDetailsCard('secChecklistPreStock')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
                <span>3. Pre-Stock Take Checklist Workflow</span>
            </span>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span id="badge-chkPreStock" class="badge badge-warning">2/5 Done</span>
                <span id="icon-secChecklistPreStock" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ COLLAPSE ]</span>
            </div>
        </div>

        <div id="body-secChecklistPreStock" class="details-card-body">
            <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 1.25rem;">
                Ensure all essential prerequisites are checked and fully completed before commencing physical scans.
            </p>
            <div class="table-wrapper">
                <table class="summary-table checklist-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">Sr.</th>
                            <th>Workflow Task Description</th>
                            <th style="width: 150px;">Status</th>
                            <th>Auditor Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">1</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Backup ERP Data & Active Audit Schema</td>
                            <td>
                                <select id="taskStatus-1" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="Pending">Pending</option>
                                    <option value="Completed" selected>Completed</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="taskRemark-1" class="checklist-input" placeholder="e.g. Local schema dump successful" oninput="saveChecklistState()">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">2</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Charge Scanner Terminals to 100%</td>
                            <td>
                                <select id="taskStatus-2" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="Pending">Pending</option>
                                    <option value="Completed" selected>Completed</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="taskRemark-2" class="checklist-input" placeholder="e.g. All 4 terminals charged" oninput="saveChecklistState()">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">3</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Setup Store Zone Boundaries & Labels</td>
                            <td>
                                <select id="taskStatus-3" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="Pending" selected>Pending</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="taskRemark-3" class="checklist-input" placeholder="Remarks..." oninput="saveChecklistState()">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">4</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Conduct Staff Briefing & Team Allocation</td>
                            <td>
                                <select id="taskStatus-4" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="Pending" selected>Pending</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="taskRemark-4" class="checklist-input" placeholder="Remarks..." oninput="saveChecklistState()">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">5</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Print Recount Adjustment Sheets</td>
                            <td>
                                <select id="taskStatus-5" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="Pending" selected>Pending</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="taskRemark-5" class="checklist-input" placeholder="Remarks..." oninput="saveChecklistState()">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STEP 4: MANDATORY REPORT ALERT CHECKLIST -->
    <!-- ========================================== -->
    <div id="panelDetailsStep-4" class="details-card hidden">
        <div class="details-card-header" onclick="toggleDetailsCard('secChecklistAlerts')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <span>4. Mandatory Report Alert Checklist</span>
            </span>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span id="badge-chkAlerts" class="badge badge-warning">1/5 Verified</span>
                <span id="icon-secChecklistAlerts" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ COLLAPSE ]</span>
            </div>
        </div>

        <div id="body-secChecklistAlerts" class="details-card-body">
            <p style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 1.25rem;">
                Verify and confirm the availability of mandatory reports before final audit reconciliation.
            </p>
            <div class="table-wrapper">
                <table class="summary-table checklist-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">Sr.</th>
                            <th>Mandatory Report Alert Target</th>
                            <th style="width: 130px;">Available</th>
                            <th style="width: 180px;">Verified By</th>
                            <th>Remarks / Action Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">1</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Zero-Scan Pending Report</td>
                            <td>
                                <select id="alertAvail-1" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="No">No</option>
                                    <option value="Yes" selected>Yes</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="alertUser-1" class="checklist-input" placeholder="Auditor Name" value="David Ocloo" oninput="saveChecklistState()">
                            </td>
                            <td>
                                <input type="text" id="alertRemark-1" class="checklist-input" placeholder="e.g. Exported successfully" oninput="saveChecklistState()">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">2</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Consolidated Discrepancies Summary</td>
                            <td>
                                <select id="alertAvail-2" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="No" selected>No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="alertUser-2" class="checklist-input" placeholder="Auditor Name" oninput="saveChecklistState()">
                            </td>
                            <td>
                                <input type="text" id="alertRemark-2" class="checklist-input" placeholder="Remarks..." oninput="saveChecklistState()">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">3</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">High-Value Scopes Report (>1000 GHC)</td>
                            <td>
                                <select id="alertAvail-3" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="No" selected>No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="alertUser-3" class="checklist-input" placeholder="Auditor Name" oninput="saveChecklistState()">
                            </td>
                            <td>
                                <input type="text" id="alertRemark-3" class="checklist-input" placeholder="Remarks..." oninput="saveChecklistState()">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">4</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Audit Staff Shift & Attendance Log</td>
                            <td>
                                <select id="alertAvail-4" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="No" selected>No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="alertUser-4" class="checklist-input" placeholder="Auditor Name" oninput="saveChecklistState()">
                            </td>
                            <td>
                                <input type="text" id="alertRemark-4" class="checklist-input" placeholder="Remarks..." oninput="saveChecklistState()">
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">5</td>
                            <td style="font-weight: 700; color: var(--color-text-main);">Scanner Recount Differential Sheet</td>
                            <td>
                                <select id="alertAvail-5" class="checklist-select" onchange="saveChecklistState()">
                                    <option value="No" selected>No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="alertUser-5" class="checklist-input" placeholder="Auditor Name" oninput="saveChecklistState()">
                            </td>
                            <td>
                                <input type="text" id="alertRemark-5" class="checklist-input" placeholder="Remarks..." oninput="saveChecklistState()">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer Navigation Buttons -->
    <div class="workspace-footer-nav" style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--color-border); margin-top: 2rem; width: 100%;">
        <button type="button" id="btnDetailsBack" onclick="handleDetailsNavigationBack()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.35rem; visibility: hidden; cursor: pointer; height: auto;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 3;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span>Back</span>
        </button>

        <button type="button" id="btnDetailsNext" onclick="handleDetailsNavigationNext()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer; height: auto;">
            <span id="btnDetailsNextText">Next</span>
            <svg id="btnDetailsNextIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 3;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

</div>

<!-- ========================================== -->
<!-- PREMIUM SECURE CONFIRMATION MODAL -->
<!-- ========================================== -->
<div id="confirmDetailsFinishModal" class="modal-backdrop hidden" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000; transition: opacity 0.2s ease;">
    <div style="background: #ffffff; border-radius: 18px; width: 100%; max-width: 480px; padding: 1.75rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); display: flex; flex-direction: column; gap: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; color: var(--color-primary);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 style="font-size: 1.2rem; font-weight: 900; color: var(--color-text-main); margin: 0; text-transform: uppercase; letter-spacing: -0.01em;">Finish & Submit Details</h3>
        </div>
        <p style="font-size: 13px; color: var(--color-text-muted); line-height: 1.5; margin: 0;">
            Are you sure you want to finish and submit all stock take information, parsed attendance/zone data, and checklists? This will freeze the details configuration.
        </p>
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="button" onclick="closeDetailsFinishModal()" class="btn btn-secondary" style="padding: 0.6rem 1.25rem; font-size: 12px; font-weight: 700; border-radius: 10px; cursor: pointer; height: auto;">Cancel</button>
            <button type="button" onclick="executeDetailsFinish()" class="btn btn-primary" style="padding: 0.6rem 1.25rem; font-size: 12px; font-weight: 700; border-radius: 10px; cursor: pointer; height: auto;">Confirm & Finish</button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- HIGH FIDELITY CSV PARSING SCRIPTS & STATE -->
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
     * Renders Parsed Data into styled merged table.
     */
    function renderParsedData(rows, type) {
        const dataRows = rows.slice(1);
        let html = "";
        let count = dataRows.length;

        if (type === 'merged') {
            const tbody = document.getElementById("tableMergedBody");
            dataRows.forEach(row => {
                html += `<tr>
                    <td style="font-family: monospace; font-weight: 800; color: var(--color-text-main); text-align: center;">${escapeHtml(row[0] || 'N/A')}</td>
                    <td style="font-weight: 800; color: var(--color-text-main);">${escapeHtml(row[1] || 'N/A')}</td>
                    <td><span class="badge badge-info">${escapeHtml(row[2] || 'Auditor')}</span></td>
                    <td style="font-family: monospace; font-weight: 700;">${escapeHtml(row[3] || 'N/A')}</td>
                    <td style="font-family: monospace; font-weight: 700; color: var(--color-primary);">${escapeHtml(row[4] || 'N/A')}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
            document.getElementById("wrapperMerged").classList.remove("hidden");
            updateBadge('secMerged', `${count} Records`, 'success');
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

        if (type === 'merged') {
            csvContent = "SI,STAFF NAME,ROLE IN AUDIT,ZONE NAME,SCANNING ID\n1,David Ocloo,IT Support,ZONE-01,SCN-801\n2,Samuel Amegbletor,Internal Audit,ZONE-02,SCN-802\n3,Auditor Kojo,Inventory,ZONE-03,SCN-803\n4,Officer Zahed,Management,ZONE-04,SCN-804";
            filename = "attendance_zone_tracker_template.csv";
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

    function saveChecklistState() {
        const preStockState = [];
        const alertState = [];

        // Save Pre-Stock Workflow
        for (let i = 1; i <= 5; i++) {
            const status = document.getElementById("taskStatus-" + i).value;
            const remark = document.getElementById("taskRemark-" + i).value;
            preStockState.push({ status, remark });
            
            // Render nice color indicators dynamically
            const select = document.getElementById("taskStatus-" + i);
            if (status === 'Completed') {
                select.style.color = '#065f46';
                select.style.backgroundColor = '#ecfdf5';
                select.style.borderColor = '#a7f3d0';
            } else {
                select.style.color = '#92400e';
                select.style.backgroundColor = '#fffbeb';
                select.style.borderColor = '#fde68a';
            }
        }

        // Save Mandatory Report Alerts
        for (let i = 1; i <= 5; i++) {
            const avail = document.getElementById("alertAvail-" + i).value;
            const user = document.getElementById("alertUser-" + i).value;
            const remark = document.getElementById("alertRemark-" + i).value;
            alertState.push({ avail, user, remark });

            const select = document.getElementById("alertAvail-" + i);
            if (avail === 'Yes') {
                select.style.color = '#065f46';
                select.style.backgroundColor = '#ecfdf5';
                select.style.borderColor = '#a7f3d0';
            } else {
                select.style.color = '#991b1b';
                select.style.backgroundColor = '#fef2f2';
                select.style.borderColor = '#fca5a5';
            }
        }

        localStorage.setItem("melcom_checklist_prestock", JSON.stringify(preStockState));
        localStorage.setItem("melcom_checklist_alerts", JSON.stringify(alertState));

        updateChecklistBadges();
        updateSidebarDetailsTracker();
    }

    function updateChecklistBadges() {
        let completedPre = 0;
        let verifiedAlert = 0;

        for (let i = 1; i <= 5; i++) {
            if (document.getElementById("taskStatus-" + i).value === 'Completed') completedPre++;
            if (document.getElementById("alertAvail-" + i).value === 'Yes') verifiedAlert++;
        }

        const badgePre = document.getElementById("badge-chkPreStock");
        const badgeAlert = document.getElementById("badge-chkAlerts");
        
        if (badgePre) {
            badgePre.innerText = `${completedPre}/5 Completed`;
            badgePre.className = `badge ${completedPre === 5 ? 'badge-success' : 'badge-warning'}`;
        }

        if (badgeAlert) {
            badgeAlert.innerText = `${verifiedAlert}/5 Verified`;
            badgeAlert.className = `badge ${verifiedAlert === 5 ? 'badge-success' : 'badge-warning'}`;
        }
    }

    function saveDetailsState(type, csvText) {
        localStorage.setItem("melcom_details_csv_" + type, csvText);
    }

    function saveDetailsVerificationCheckbox() {
        const chk = document.getElementById("chkConfirmSetupVerified");
        localStorage.setItem("melcom_details_setup_verified", chk.checked ? "true" : "false");
    }

    function loadDetailsState() {
        // 0. Load submitted Stock Audit parameters
        const isSubmitted = localStorage.getItem("melcom_stock_audit_submitted") === "true";
        const auditDataRaw = localStorage.getItem("melcom_stock_audit_data");
        const summaryCard = document.getElementById("stockAuditSubmittedSummary");

        if (isSubmitted && auditDataRaw && summaryCard) {
            try {
                const data = JSON.parse(auditDataRaw);
                document.getElementById("sumShopCode").innerText = data.shopCode || "-";
                document.getElementById("sumStockDate").innerText = data.stockDate || "-";
                document.getElementById("sumAuditType").innerText = data.auditType || "-";
                document.getElementById("sumAuditMode").innerText = data.auditMode || "-";
                document.getElementById("sumDepts").innerText = data.depts || "None";
                document.getElementById("sumSegments").innerText = data.groups || "None";
                
                // Show summary
                summaryCard.classList.remove("hidden");
                
                // Load verification checkbox
                const chk = document.getElementById("chkConfirmSetupVerified");
                if (chk) {
                    chk.checked = localStorage.getItem("melcom_details_setup_verified") === "true";
                }
            } catch(e){}
        } else if (summaryCard) {
            summaryCard.classList.add("hidden");
        }

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

        // 2. Load CSV Table
        const csv = localStorage.getItem("melcom_details_csv_merged");
        if (csv) {
            const rows = parseCsvText(csv);
            if (rows && rows.length > 0) {
                renderParsedData(rows, 'merged');
            }
        }
        
        // 3. Load Checklist states
        const prestock = localStorage.getItem("melcom_checklist_prestock");
        const alerts = localStorage.getItem("melcom_checklist_alerts");

        if (prestock) {
            try {
                const state = JSON.parse(prestock);
                state.forEach((item, index) => {
                    const i = index + 1;
                    if (document.getElementById("taskStatus-" + i)) {
                        document.getElementById("taskStatus-" + i).value = item.status || "Pending";
                        document.getElementById("taskRemark-" + i).value = item.remark || "";
                    }
                });
            } catch(e) {}
        }

        if (alerts) {
            try {
                const state = JSON.parse(alerts);
                state.forEach((item, index) => {
                    const i = index + 1;
                    if (document.getElementById("alertAvail-" + i)) {
                        document.getElementById("alertAvail-" + i).value = item.avail || "No";
                        document.getElementById("alertUser-" + i).value = item.user || "";
                        document.getElementById("alertRemark-" + i).value = item.remark || "";
                    }
                });
            } catch(e) {}
        }

        saveChecklistState(); // Sync badge colors and values immediately
        updateSidebarDetailsTracker();
        updateDetailsWizardState();
    }

    function updateSidebarDetailsTracker() {
        const tracker = document.getElementById("sidebarDetailsProgressText");
        const fill = document.getElementById("sidebarDetailsProgress");
        if (!tracker) return;

        let completed = 0;
        
        // Check stock take info
        const storeName = document.getElementById("infoStoreName").value.trim();
        const auditLead = document.getElementById("infoAuditLead").value.trim();
        if (storeName !== "" && auditLead !== "") completed++;

        // Check Merged CSV
        if (localStorage.getItem("melcom_details_csv_merged")) completed++;

        // Check Checklist Pre-stock
        let completedPre = 0;
        for (let i = 1; i <= 5; i++) {
            if (document.getElementById("taskStatus-" + i) && document.getElementById("taskStatus-" + i).value === 'Completed') completedPre++;
        }
        if (completedPre === 5) completed++;

        // Check Checklist Alerts
        let verifiedAlert = 0;
        for (let i = 1; i <= 5; i++) {
            if (document.getElementById("alertAvail-" + i) && document.getElementById("alertAvail-" + i).value === 'Yes') verifiedAlert++;
        }
        if (verifiedAlert === 5) completed++;

        const percent = Math.round((completed / 4) * 100);
        tracker.innerText = `${completed}/4 Completed`;
        if (fill) fill.style.width = `${percent}%`;
    }

    // -------------------------------------------------------------
    // DETAILS WIZARD SEQUENTIAL NAVIGATION
    // -------------------------------------------------------------
    let activeDetailsStep = 1;

    function handleDetailsNavigationNext() {
        if (activeDetailsStep === 4) {
            showDetailsFinishModal();
            return;
        }
        activeDetailsStep++;
        updateDetailsWizardState();
    }

    function handleDetailsNavigationBack() {
        if (activeDetailsStep === 1) return;
        activeDetailsStep--;
        updateDetailsWizardState();
    }

    function showDetailsFinishModal() {
        document.getElementById("confirmDetailsFinishModal").classList.remove("hidden");
    }

    function closeDetailsFinishModal() {
        document.getElementById("confirmDetailsFinishModal").classList.add("hidden");
    }

    function executeDetailsFinish() {
        closeDetailsFinishModal();
        localStorage.setItem("melcom_details_finished", "true");
        alert("Congratulations! All stock take details and checklists have been successfully completed and locked.");
    }

    function updateDetailsWizardState() {
        // Toggle card panels
        for (let i = 1; i <= 4; i++) {
            const panel = document.getElementById("panelDetailsStep-" + i);
            if (panel) {
                if (i === activeDetailsStep) {
                    panel.classList.remove("hidden");
                } else {
                    panel.classList.add("hidden");
                }
            }
        }

        // Toggle back buttons
        const btnBack = document.getElementById("btnDetailsBack");
        if (btnBack) {
            if (activeDetailsStep === 1) {
                btnBack.style.visibility = "hidden";
            } else {
                btnBack.style.visibility = "visible";
            }
        }

        // Toggle next buttons
        const btnNextText = document.getElementById("btnDetailsNextText");
        const btnNextIcon = document.getElementById("btnDetailsNextIcon");
        if (btnNextText) {
            if (activeDetailsStep === 4) {
                btnNextText.innerText = "Finish";
                if (btnNextIcon) btnNextIcon.style.display = "none";
            } else {
                btnNextText.innerText = "Next";
                if (btnNextIcon) btnNextIcon.style.display = "inline";
            }
        }

        updateSidebarDetailsStepNodes();
    }

    function updateSidebarDetailsStepNodes() {
        for (let i = 1; i <= 4; i++) {
            const circle = document.getElementById("circleDetails-" + i);
            const text = document.getElementById("textDetails-" + i);
            if (!circle || !text) continue;

            circle.className = "step-circle";
            text.className = "step-text";

            if (i < activeDetailsStep) {
                circle.classList.add("completed");
                text.classList.add("completed");
                circle.innerText = "✓";
            } else if (i === activeDetailsStep) {
                circle.classList.add("active");
                text.classList.add("active");
                circle.innerText = i;
            } else {
                circle.classList.add("upcoming");
                text.classList.add("upcoming");
                circle.innerText = i;
            }
        }
    }

    // Auto-run state loaders on dom content load
    window.addEventListener("DOMContentLoaded", () => {
        loadDetailsState();
    });
</script>
