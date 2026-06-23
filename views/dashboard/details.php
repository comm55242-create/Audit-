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
    <div id="stockAuditSubmittedSummary" class="details-card hidden" style="background: var(--color-primary-light); border: 1px solid var(--color-primary-ring); border-radius: 18px; width: 100%; overflow: hidden;">
        <div class="details-card-header" onclick="toggleDetailsCard('sumSetupInfo')" style="cursor: pointer; border-bottom: none; background: transparent; padding: 1.25rem 1.5rem;">
            <span style="font-size: 0.9rem; font-weight: 900; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.5; color: var(--color-primary);"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Active Stock Audit Setup Parameters</span>
            </span>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span class="badge badge-success" style="font-size: 10px; font-weight: 800; text-transform: uppercase;">Submitted</span>
                <span id="icon-sumSetupInfo" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ EXPAND ]</span>
            </div>
        </div>
        
        <div id="body-sumSetupInfo" class="details-card-body hidden" style="padding-top: 0; padding-bottom: 1.5rem; padding-left: 1.5rem; padding-right: 1.5rem; border-top: 1px solid var(--color-border); background: var(--color-primary-light);">
            <div style="display: flex; flex-direction: column; gap: 1.25rem; width: 100%; margin-top: 1rem;">
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
        </div>
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
                
                <!-- Group I: Store & Audit Info -->
                <div style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                    <h4 style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: var(--color-primary); margin-bottom: 0.25rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: var(--color-primary-light); color: var(--color-primary); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900;">I</span>
                        <span>Store & Audit Info</span>
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field">
                            <label class="form-label">Store Name</label>
                            <input type="text" id="infoStoreName" class="form-input" readonly style="background-color: #f1f5f9; cursor: not-allowed; font-weight: 600; color: var(--color-text-main);">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Store Code</label>
                            <input type="text" id="infoStoreCode" class="form-input" readonly style="background-color: #f1f5f9; cursor: not-allowed; font-weight: 600; font-family: monospace; color: var(--color-text-main);">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-field">
                            <label class="form-label">Audit Start Date</label>
                            <input type="date" id="infoAuditStartDate" class="form-input" oninput="saveStockInfoState()">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Audit End Date</label>
                            <input type="date" id="infoAuditEndDate" class="form-input" oninput="saveStockInfoState()">
                        </div>
                    </div>
                </div>

                <!-- Group II: Personnel & Uploads -->
                <div style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                    <h4 style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: var(--color-primary); margin-bottom: 0.25rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: var(--color-primary-light); color: var(--color-primary); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900;">II</span>
                        <span>Store & Operations and Audit Personnel</span>
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div class="form-field">
                            <label class="form-label">Store Manager Name</label>
                            <input type="text" id="infoStoreManager" class="form-input" placeholder="e.g. Samuel Kojo" oninput="saveStockInfoState()">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Operations Manager</label>
                            <input type="text" id="infoOpsManager" class="form-input" placeholder="e.g. David Mensah" oninput="saveStockInfoState()">
                        </div>
                        <div class="form-field">
                            <label class="form-label">Audit Lead</label>
                            <input type="text" id="infoAuditLead" class="form-input" placeholder="e.g. David Ocloo" oninput="saveStockInfoState()">
                        </div>
                    </div>

                    <div style="margin-top: 1rem; border-top: 1px dashed var(--color-border); padding-top: 1rem;">
                        <h5 style="font-size: 12px; font-weight: 800; color: var(--color-text-main); margin-bottom: 0.75rem;">Staff & Auditor Rosters Upload</h5>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <!-- Audit Team -->
                            <div class="upload-zone" onclick="triggerFileInput('fileAuditStaff')" ondragover="handleDragOver(event, this)" ondragleave="handleDragLeave(event, this)" ondrop="handleDrop(event, this, 'audit_staff')" style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 1.25rem; text-align: center; cursor: pointer; transition: all 0.2s ease; background: #ffffff;">
                                <div style="font-size: 13px; font-weight: 800; color: var(--color-primary); margin-bottom: 0.25rem;">Upload Audit Team List</div>
                                <div id="countAuditTeam" style="font-size: 20px; font-weight: 900; color: var(--color-text-main); margin: 0.5rem 0;">0 Audit</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);"><a href="javascript:void(0)" onclick="event.stopPropagation(); downloadCsvTemplate('audit_staff')" style="color: var(--color-primary); text-decoration: underline;">Download Format</a></div>
                                <input type="file" id="fileAuditStaff" accept=".csv" style="display: none;" onchange="handleFileSelect(event, 'audit_staff')">
                            </div>

                            <!-- Ops / Shop Staff -->
                            <div class="upload-zone" onclick="triggerFileInput('fileShopStaff')" ondragover="handleDragOver(event, this)" ondragleave="handleDragLeave(event, this)" ondrop="handleDrop(event, this, 'shop_staff')" style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 1.25rem; text-align: center; cursor: pointer; transition: all 0.2s ease; background: #ffffff;">
                                <div style="font-size: 13px; font-weight: 800; color: var(--color-primary); margin-bottom: 0.25rem;">Upload Shop Staff List</div>
                                <div id="countShopStaff" style="font-size: 20px; font-weight: 900; color: var(--color-text-main); margin: 0.5rem 0;">0 Shop Staff</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);"><a href="javascript:void(0)" onclick="event.stopPropagation(); downloadCsvTemplate('shop_staff')" style="color: var(--color-primary); text-decoration: underline;">Download Format</a></div>
                                <input type="file" id="fileShopStaff" accept=".csv" style="display: none;" onchange="handleFileSelect(event, 'shop_staff')">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group III: Zone Details -->
                <div style="background: #f8fafc; border: 1px solid var(--color-border); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
                    <h4 style="font-size: 11px; font-weight: 900; text-transform: uppercase; color: var(--color-primary); margin-bottom: 0.25rem; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: var(--color-primary-light); color: var(--color-primary); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900;">III</span>
                        <span>Zone Details</span>
                    </h4>
                    
                    <div class="upload-zone" onclick="triggerFileInput('fileZones')" ondragover="handleDragOver(event, this)" ondragleave="handleDragLeave(event, this)" ondrop="handleDrop(event, this, 'zones')" style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s ease; background: #ffffff;">
                        <div style="font-size: 14px; font-weight: 800; color: var(--color-primary); margin-bottom: 0.5rem;">Upload Zones List</div>
                        <p style="font-size: 11px; color: var(--color-text-muted); margin-bottom: 0.75rem;">(Must contain Zone Number and Category Name)</p>
                        <div id="countZones" style="font-size: 24px; font-weight: 900; color: var(--color-text-main); margin-bottom: 1rem;">0 Zones Mapped</div>
                        <div style="font-size: 12px;"><a href="javascript:void(0)" onclick="event.stopPropagation(); downloadCsvTemplate('zones')" class="btn btn-secondary" style="padding: 0.4rem 1rem; border-radius: 6px; text-decoration: none;">Download Template Format</a></div>
                        <input type="file" id="fileZones" accept=".csv" style="display: none;" onchange="handleFileSelect(event, 'zones')">
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STEP 2: PRE-STOCK TAKE CHECKLIST WORKFLOW -->
    <!-- ========================================== -->
    <div id="panelDetailsStep-2" class="details-card hidden">
        <div class="details-card-header" onclick="toggleDetailsCard('secChecklistPreStock')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
                <span>2. Pre-Stock Take Checklist Workflow</span>
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
            <div class="table-wrapper" style="overflow-x: auto;">
                <table class="summary-table checklist-table" style="width: 100%; border-collapse: collapse; min-width: 900px;">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">SR No</th>
                            <th>Check Point</th>
                            <th style="width: 100px; text-align: center;">Status <span style="color: #ef4444; font-weight: bold;">*</span><br><small>(Auditor)</small></th>
                            <th style="width: 350px; text-align: center;">Remarks <span style="color: #ef4444; font-weight: bold;">*</span><br><small>(Auditor)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Inwards -->
                        <tr><td colspan="4" style="background:#f1f5f9; font-weight:800; color:var(--color-primary); padding: 0.5rem 1rem; font-size:12px; text-transform:uppercase;">Inwards</td></tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">1</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All Warehouse Receipts being closed/posted in the ERP</td>
                            <td><select id="auStatus-1" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-1" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">2</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All Store To Store Receipts being closed/posted in the ERP</td>
                            <td><select id="auStatus-2" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-2" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">3</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All Direct Supply Receipts being closed/posted in the ERP</td>
                            <td><select id="auStatus-3" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-3" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>

                        <!-- Outwards -->
                        <tr><td colspan="4" style="background:#f1f5f9; font-weight:800; color:var(--color-primary); padding: 0.5rem 1rem; font-size:12px; text-transform:uppercase;">Outwards</td></tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">4</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All Returns to Warehouse being closed/posted in the ERP</td>
                            <td><select id="auStatus-4" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-4" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">5</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All Store To Store Transfer (STST) being closed/posted in the ERP</td>
                            <td><select id="auStatus-5" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-5" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">6</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All Returns to Direct Supply being closed/posted in the ERP</td>
                            <td><select id="auStatus-6" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-6" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>

                        <!-- Outwards (Damage/Expiry/Repair) -->
                        <tr><td colspan="4" style="background:#f1f5f9; font-weight:800; color:var(--color-primary); padding: 0.5rem 1rem; font-size:12px; text-transform:uppercase;">Outwards (Damage/Expiry/Repair)</td></tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">7</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">Warehouse/Direct Supply being closed/posted in the ERP</td>
                            <td><select id="auStatus-7" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-7" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">8</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All stock transfer for Repair to Service Center being closed/posted in the system</td>
                            <td><select id="auStatus-8" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-8" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>

                        <!-- Negative Inventory -->
                        <tr><td colspan="4" style="background:#f1f5f9; font-weight:800; color:var(--color-primary); padding: 0.5rem 1rem; font-size:12px; text-transform:uppercase;">Negative Inventory</td></tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">9</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All Negative Inventory being booked/posted in the ERP</td>
                            <td><select id="auStatus-9" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-9" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>

                        <!-- Fixed Assets/Consumption/Sampling -->
                        <tr><td colspan="4" style="background:#f1f5f9; font-weight:800; color:var(--color-primary); padding: 0.5rem 1rem; font-size:12px; text-transform:uppercase;">Fixed Assets/Consumption/Sampling</td></tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">10</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All Fixed assets assigned to the store are correctly recorded in the system</td>
                            <td><select id="auStatus-10" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-10" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">11</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All store consumption stock being booked/posted in the ERP</td>
                            <td><select id="auStatus-11" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-11" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">12</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">All sampling stock being booked/posted in the ERP</td>
                            <td><select id="auStatus-12" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-12" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>

                        <!-- Others -->
                        <tr><td colspan="4" style="background:#f1f5f9; font-weight:800; color:var(--color-primary); padding: 0.5rem 1rem; font-size:12px; text-transform:uppercase;">Others</td></tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">13</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">Any item Billed but laying in the store, pending for delivery to the customer?</td>
                            <td><select id="auStatus-13" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-13" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: 800; color: var(--color-text-light);">14</td>
                            <td style="font-weight: 700; color: var(--color-text-main); font-size:12px; line-height:1.4;">Is Annexure 1.0 properly filled & signed by the Store/Operation/IT/Audit Manager</td>
                            <td><select id="auStatus-14" class="checklist-select" onchange="saveChecklistState()"><option value="" disabled selected>-</option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><input type="text" id="auRemark-14" class="checklist-input" placeholder="Remarks" oninput="saveChecklistState()"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STEP 3: PRINT & SIGN-OFF GENERATION -->
    <!-- ========================================== -->
    <div id="panelDetailsStep-3" class="details-card hidden">
        <div class="details-card-header" onclick="toggleDetailsCard('secPrintSignOff')">
            <span class="details-card-title">
                <span class="details-card-icon" style="display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                </span>
                <span>3. Print</span>
            </span>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span id="badge-chkPrint" class="badge badge-success">Ready</span>
                <span id="icon-secPrintSignOff" style="font-size: 11px; font-weight: 800; color: var(--color-text-light);">[ COLLAPSE ]</span>
            </div>
        </div>

        <div id="body-secPrintSignOff" class="details-card-body" style="text-align: center; padding: 2rem;">
            <p style="font-size: 13px; color: var(--color-text-muted); margin-bottom: 1.5rem; max-width: 600px; margin-left: auto; margin-right: auto;">
                Generate the final summary documents containing the setup configurations, team rosters, and zone mapping for ink signature approval.
            </p>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; max-width: 600px; margin: 0 auto;">
                <button type="button" onclick="generatePrintableSignOff('checklist')" class="btn btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 13px; padding: 0.75rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/></svg>
                    <span>Print Pre-Stock Checklist</span>
                </button>
                <button type="button" onclick="generatePrintableSignOff('audit_staff')" class="btn btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 13px; padding: 0.75rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/></svg>
                    <span>Print Audit Staff Roster</span>
                </button>
                <button type="button" onclick="generatePrintableSignOff('shop_staff')" class="btn btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 13px; padding: 0.75rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/></svg>
                    <span>Print Shop Staff Roster</span>
                </button>
                <button type="button" onclick="generatePrintableSignOff('zones')" class="btn btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 13px; padding: 0.75rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/></svg>
                    <span>Print Zone Mapping</span>
                </button>
                <button type="button" onclick="generatePrintableSignOff('zone_barcodes')" class="btn btn-primary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 13px; padding: 0.75rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Print Zone Barcodes</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Footer Navigation Buttons -->
    <div class="workspace-footer-nav" style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid var(--color-border); margin-top: 2rem; width: 100%;">
        <button type="button" id="btnDetailsBack" onclick="handleDetailsNavigationBack()" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.35rem; visibility: hidden; cursor: pointer; height: auto;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 3;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span>Back</span>
        </button>

        <div style="display: flex; gap: 1rem; align-items: center;">
            <button type="button" id="btnStartStockTake" onclick="startStockTake()" class="btn btn-secondary hidden" style="display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer; height: auto; border-color: var(--color-primary); color: var(--color-primary);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18"/></svg>
                <span>Start Stock-Take</span>
            </button>
            <button type="button" id="btnDetailsNext" onclick="handleDetailsNavigationNext()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer; height: auto;">
                <span id="btnDetailsNextText">Next</span>
                <svg id="btnDetailsNextIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 3;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- PREMIUM SECURE CONFIRMATION MODAL -->
<!-- ========================================== -->
<!-- VALIDATION WARNING MODAL (CHECKLIST) -->
<!-- ========================================== -->
<div id="validationWarningModal" class="modal-backdrop hidden" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000; transition: opacity 0.2s ease;">
    <div style="background: #ffffff; border-radius: 18px; width: 100%; max-width: 480px; padding: 1.75rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); display: flex; flex-direction: column; gap: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; color: #ef4444;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <h3 style="font-size: 1.2rem; font-weight: 900; color: #0f172a; margin: 0; text-transform: uppercase; letter-spacing: -0.01em;">Action Required</h3>
        </div>
        <p style="font-size: 13px; color: #475569; line-height: 1.5; margin: 0;">
            Please make sure you have selected a <strong>Status</strong> and provided <strong>Remarks</strong> for all 14 checklist items before continuing to the next step.
        </p>
        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem;">
            <button type="button" onclick="document.getElementById('validationWarningModal').classList.add('hidden')" class="btn btn-secondary" style="padding: 0.6rem 1.25rem; font-size: 12px; font-weight: 700; border-radius: 10px; cursor: pointer; height: auto;">Understood</button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- CONFIRM FINISH MODAL -->
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
<!-- PREMIUM SUCCESS CONFIRMATION MODAL -->
<!-- ========================================== -->
<div id="successDetailsFinishModal" class="modal-backdrop hidden" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000; transition: opacity 0.2s ease;">
    <div style="background: #ffffff; border-radius: 18px; width: 100%; max-width: 440px; padding: 2rem 1.75rem; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); display: flex; flex-direction: column; gap: 1rem; align-items: center; text-align: center;">
        <div style="display: flex; align-items: center; justify-content: center; width: 64px; height: 64px; border-radius: 50%; background: #dcfce7; color: #16a34a; margin-bottom: 0.25rem;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; stroke-width: 3;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 style="font-size: 1.4rem; font-weight: 900; color: var(--color-text-main); margin: 0; text-transform: uppercase; letter-spacing: -0.01em;">Confirmation</h3>
        <p style="font-size: 14px; color: var(--color-text-muted); line-height: 1.6; margin: 0;">
            All stock take details and checklists have been successfully recorded. The audit preparation is now fully confirmed.
        </p>
        <div style="display: flex; justify-content: center; width: 100%; margin-top: 0.75rem;">
            <button type="button" onclick="closeSuccessFinishModal()" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 13px; font-weight: 700; border-radius: 10px; cursor: pointer; height: auto; width: 100%;">Acknowledge & Close</button>
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

    function renderParsedData(rows, type) {
        const dataRows = rows.slice(1);
        const count = dataRows.length;

        if (type === 'audit_staff') {
            document.getElementById("countAuditTeam").innerText = `${count} Audit Staff`;
        } else if (type === 'shop_staff') {
            document.getElementById("countShopStaff").innerText = `${count} Shop Staff`;
        } else if (type === 'zones') {
            document.getElementById("countZones").innerText = `${count} Zones Mapped`;
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

    function downloadCsvTemplate(type) {
        let csvContent = "";
        let filename = "";

        if (type === 'audit_staff') {
            csvContent = "SI,STAFF NAME,ROLE IN AUDIT\n1,David Ocloo,IT Support\n2,Samuel Amegbletor,Internal Audit";
            filename = "audit_team_template.csv";
        } else if (type === 'shop_staff') {
            csvContent = "SI,STAFF NAME,DESIGNATION\n1,Kwame,Cashier\n2,Ama,Supervisor";
            filename = "shop_staff_template.csv";
        } else if (type === 'zones') {
            csvContent = "ZONE NUMBER,CATEGORY NAME\nZONE-01,Electronics\nZONE-02,Groceries";
            filename = "zones_template.csv";
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
            storeManager: document.getElementById("infoStoreManager").value,
            opsManager: document.getElementById("infoOpsManager").value,
            auditLead: document.getElementById("infoAuditLead").value,
            auditStartDate: document.getElementById("infoAuditStartDate").value,
            auditEndDate: document.getElementById("infoAuditEndDate").value
        };
        localStorage.setItem("melcom_details_info", JSON.stringify(state));
        updateSidebarDetailsTracker();
    }

    function saveChecklistState() {
        const preStockState = [];

        // Save Pre-Stock Workflow
        for (let i = 1; i <= 14; i++) {
            const auStatus = document.getElementById("auStatus-" + i).value;
            const auRemark = document.getElementById("auRemark-" + i).value;
            preStockState.push({ auStatus, auRemark });
            
            // Render nice color indicators dynamically
            const auSel = document.getElementById("auStatus-" + i);
            if (auStatus === 'Yes') {
                auSel.style.color = '#065f46'; auSel.style.backgroundColor = '#ecfdf5'; auSel.style.borderColor = '#a7f3d0';
            } else if (auStatus === 'No') {
                auSel.style.color = '#991b1b'; auSel.style.backgroundColor = '#fef2f2'; auSel.style.borderColor = '#fca5a5';
            } else {
                auSel.style.color = ''; auSel.style.backgroundColor = ''; auSel.style.borderColor = '';
            }
        }

        localStorage.setItem("melcom_checklist_prestock", JSON.stringify(preStockState));

        updateChecklistBadges();
        updateSidebarDetailsTracker();
    }

    function updateChecklistBadges() {
        let completedPre = 0;

        for (let i = 1; i <= 14; i++) {
            if (document.getElementById("auStatus-" + i).value !== '') {
                completedPre++;
            }
        }

        const badgePre = document.getElementById("badge-chkPreStock");
        
        if (badgePre) {
            badgePre.innerText = `${completedPre}/14 Done`;
            badgePre.className = `badge ${completedPre === 14 ? 'badge-success' : 'badge-warning'}`;
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
                
                // Auto-populate Store Code & Name from Setup Data
                const elCode = document.getElementById("infoStoreCode");
                const elName = document.getElementById("infoStoreName");
                if (elCode) elCode.value = data.shopCode || "";
                if (elName) elName.value = data.shopCodeDescription || data.shopName || "";
                
                // Show summary
                summaryCard.classList.remove("hidden");
                
                // Load verification checkbox
                const chk = document.getElementById("chkConfirmSetupVerified");
                if (chk) {
                    const savedVal = localStorage.getItem("melcom_details_setup_verified");
                    if (savedVal === null) {
                        chk.checked = true;
                        localStorage.setItem("melcom_details_setup_verified", "true");
                    } else {
                        chk.checked = savedVal === "true";
                    }
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
                if(document.getElementById("infoStoreManager")) document.getElementById("infoStoreManager").value = state.storeManager || "";
                if(document.getElementById("infoOpsManager")) document.getElementById("infoOpsManager").value = state.opsManager || "";
                if(document.getElementById("infoAuditLead")) document.getElementById("infoAuditLead").value = state.auditLead || "";
                if(document.getElementById("infoAuditStartDate")) document.getElementById("infoAuditStartDate").value = state.auditStartDate || "";
                if(document.getElementById("infoAuditEndDate")) document.getElementById("infoAuditEndDate").value = state.auditEndDate || "";
            } catch(e) {}
        }

        // 2. Load CSV Tables (Counts)
        ['audit_staff', 'shop_staff', 'zones'].forEach(type => {
            const csv = localStorage.getItem("melcom_details_csv_" + type);
            if (csv) {
                const rows = parseCsvText(csv);
                if (rows && rows.length > 0) {
                    renderParsedData(rows, type);
                }
            }
        });
        
        // 3. Load Checklist states
        const prestock = localStorage.getItem("melcom_checklist_prestock");

        if (prestock) {
            try {
                const state = JSON.parse(prestock);
                state.forEach((item, index) => {
                    const i = index + 1;
                    if (document.getElementById("auStatus-" + i)) {
                        document.getElementById("auStatus-" + i).value = item.auStatus || "";
                        document.getElementById("auRemark-" + i).value = item.auRemark || "";
                    }
                });
            } catch(e) {}
        }

        updateChecklistBadges();
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

        // Check CSVs
        if (localStorage.getItem("melcom_details_csv_audit_staff") && 
            localStorage.getItem("melcom_details_csv_shop_staff") && 
            localStorage.getItem("melcom_details_csv_zones")) {
            completed++;
        }

        // Check Checklist Pre-stock
        let completedPre = 0;
        for (let i = 1; i <= 14; i++) {
            if (document.getElementById("auStatus-" + i) && document.getElementById("auStatus-" + i).value !== '') completedPre++;
        }
        if (completedPre === 14) completed++;

        const percent = Math.round((completed / 3) * 100);
        tracker.innerText = `${completed}/3 Completed`;
        if (fill) fill.style.width = `${percent}%`;
    }

    // -------------------------------------------------------------
    // DETAILS WIZARD SEQUENTIAL NAVIGATION
    // -------------------------------------------------------------
    let activeDetailsStep = 1;

    function handleDetailsNavigationNext() {
        if (activeDetailsStep === 2) {
            let isValid = true;
            for (let i = 1; i <= 14; i++) {
                const statusSelect = document.getElementById("auStatus-" + i);
                const remarkInput = document.getElementById("auRemark-" + i);
                if (statusSelect && remarkInput) {
                    if (!statusSelect.value || statusSelect.value.trim() === "" || !remarkInput.value || remarkInput.value.trim() === "") {
                        isValid = false;
                        break;
                    }
                }
            }
            if (!isValid) {
                document.getElementById('validationWarningModal').classList.remove('hidden');
                return;
            }
        }
        if (activeDetailsStep === 3) {
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

    function closeSuccessFinishModal() {
        document.getElementById("successDetailsFinishModal").classList.add("hidden");
    }

    function executeDetailsFinish() {
        closeDetailsFinishModal();
        localStorage.setItem("melcom_details_finished", "true");
        updateDetailsWizardState();
        document.getElementById("successDetailsFinishModal").classList.remove("hidden");
    }

    function generatePrintableSignOff(type = 'checklist') {
        const jsBarcodeSource = <?php echo json_encode(file_get_contents(__DIR__ . '/jsbarcode.min.js')); ?>;
        const storeCode = document.getElementById("infoStoreCode").value || "N/A";
        const storeName = document.getElementById("infoStoreName").value || "N/A";
        const auditStartDate = document.getElementById("infoAuditStartDate").value || "N/A";
        const opsManager = document.getElementById("infoOpsManager").value || "N/A";
        const storeManager = document.getElementById("infoStoreManager").value || "N/A";
        const auditLead = document.getElementById("infoAuditLead").value || "N/A";
        
        const countAudit = document.getElementById("countAuditTeam").innerText.replace(" Audit Staff", "");
        const countShop = document.getElementById("countShopStaff").innerText.replace(" Shop Staff", "");
        const countZones = document.getElementById("countZones").innerText.replace(" Zones Mapped", "");

        let contentHTML = "";
        let docTitle = "";

        if (type === 'checklist') {
            docTitle = "Pre-Stock Take Checklist";
            const labels = [
                "All Warehouse Receipts being closed/posted in the ERP",
                "All Store To Store Receipts being closed/posted in the ERP",
                "All Direct Supply Receipts being closed/posted in the ERP",
                "All Returns to Warehouse being closed/posted in the ERP",
                "All Store To Store Transfer (STST) being closed/posted in the ERP",
                "All Returns to Direct Supply being closed/posted in the ERP",
                "Warehouse/Direct Supply being closed/posted in the ERP",
                "All stock transfer for Repair to Service Center being closed/posted in the system",
                "All Negative Inventory being booked/posted in the ERP",
                "All Fixed assets assigned to the store are correctly recorded in the system",
                "All store consumption stock being booked/posted in the ERP",
                "All sampling stock being booked/posted in the ERP",
                "Any item Billed but laying in the store, pending for delivery to the customer?",
                "Is Annexure 1.0 properly filled & signed by the Store/Operation/IT/Audit Manager"
            ];
            
            contentHTML = `
                <h3>Pre-Stock Take Checklist</h3>
                <div style="margin-bottom: 20px; font-size: 13px;">
                    <b>Audit Start Date:</b> ${auditStartDate} <br>
                    <b>Audit End Date:</b> ${document.getElementById('infoAuditEndDate')?.value || 'N/A'}
                </div>
                <table class="checklist-table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">SR No</th>
                            <th>Check Point</th>
                            <th style="width: 80px; text-align: center;">Status (AU)</th>
                            <th style="width: 350px;">Remarks (AU)</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            for (let i = 1; i <= 14; i++) {
                const auStat = document.getElementById("auStatus-" + i)?.value || "-";
                const auRem = document.getElementById("auRemark-" + i)?.value || "";
                
                contentHTML += `
                    <tr>
                        <td style="text-align: center; border: 1px solid #cbd5e1; padding: 4px;">${i}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 4px;">${labels[i-1]}</td>
                        <td style="text-align: center; border: 1px solid #cbd5e1; padding: 4px; font-weight: bold;">${auStat}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 4px;">${auRem}</td>
                    </tr>
                `;
            }
            contentHTML += `</tbody></table>`;
        } else if (type === 'zone_barcodes') {
            docTitle = "Zone Barcodes";
            contentHTML = `<h3>Zone Barcodes (Scan for Zone & Category)</h3>`;
            
            const csvText = localStorage.getItem("melcom_details_csv_zones");
            if (!csvText) {
                contentHTML += `<p style="color: #dc2626; font-weight: bold; margin-top: 1rem;">No data uploaded for zones. Please upload the CSV in Step 1.</p>`;
            } else {
                const rows = parseCsvText(csvText);
                if (rows.length > 1) {
                    // Try to auto-detect columns, default to 0 and 1
                    let zoneIdx = 0;
                    let catIdx = 1;
                    
                    const headers = rows[0].map(h => h.toUpperCase());
                    headers.forEach((h, idx) => {
                        if (h.includes("ZONE")) zoneIdx = idx;
                        if (h.includes("CATEGORY")) catIdx = idx;
                    });

                    contentHTML += `<div style="display: block; width: 100%;">`;
                    
                    for (let i = 1; i < rows.length; i++) {
                        if (!rows[i] || rows[i].length < 2 || !rows[i][0]) continue;
                        const zoneVal = rows[i][zoneIdx] ? rows[i][zoneIdx].trim() : "";
                        const catVal = rows[i][catIdx] ? rows[i][catIdx].trim() : "";
                        if (!zoneVal) continue;
                        
                        // Combine them as requested: "ZONE NUMBER AND CATEGORY NAME"
                        // Code128 handles alphanumeric and dashes well
                        const barcodeValue = `${zoneVal} - ${catVal}`.substring(0, 40); // limit length to avoid scanner issues
                        
                        // Add page break and increase barcode dimensions for "BIG" sizing
                        contentHTML += `
                            <div style="border: 2px dashed #cbd5e1; padding: 3rem; border-radius: 8px; text-align: center; background: #ffffff; margin-bottom: 2rem; page-break-after: always; break-after: page; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 80vh;">
                                <div style="font-weight: 900; font-size: 32px; margin-bottom: 1rem; color: #1e293b;">ZONE: ${escapeHtml(zoneVal)}</div>
                                <div style="font-size: 24px; color: #475569; margin-bottom: 3rem;">${escapeHtml(catVal)}</div>
                                <svg class="barcode-svg" 
                                     jsbarcode-format="CODE128" 
                                     jsbarcode-value="${escapeHtml(barcodeValue)}" 
                                     jsbarcode-textmargin="0" 
                                     jsbarcode-height="150"
                                     jsbarcode-width="3"
                                     jsbarcode-fontsize="24"
                                     jsbarcode-fontoptions="bold">
                                </svg>
                            </div>
                        `;
                    }
                    contentHTML += `</div>`;
                } else {
                    contentHTML += `<p style="color: #dc2626;">The uploaded Zone CSV appears to be empty.</p>`;
                }
            }
        } else {
            let typeName = "";
            if (type === 'audit_staff') typeName = "Audit Staff Roster";
            if (type === 'shop_staff') typeName = "Shop Staff Roster";
            if (type === 'zones') typeName = "Zone Mapping";
            
            docTitle = typeName;
            contentHTML = `<h3>${typeName}</h3>`;
            
            const csvText = localStorage.getItem("melcom_details_csv_" + type);
            if (!csvText) {
                contentHTML += `<p style="color: #dc2626; font-weight: bold; margin-top: 1rem;">No data uploaded for this section. Please upload the CSV in Step 1.</p>`;
            } else {
                const rows = parseCsvText(csvText);
                if (rows.length > 0) {
                    contentHTML += `<table class="checklist-table"><thead><tr>`;
                    rows[0].forEach(header => {
                        contentHTML += `<th style="text-align: left;">${escapeHtml(header)}</th>`;
                    });
                    contentHTML += `</tr></thead><tbody>`;
                    
                    for (let i = 1; i < rows.length; i++) {
                        contentHTML += `<tr>`;
                        rows[i].forEach(cell => {
                            contentHTML += `<td style="border: 1px solid #cbd5e1; padding: 4px;">${escapeHtml(cell)}</td>`;
                        });
                        contentHTML += `</tr>`;
                    }
                    contentHTML += `</tbody></table>`;
                }
            }
        }

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert("Your browser is blocking the popup. Please allow popups for this site to print.");
            return;
        }

        printWindow.document.write(`
            <html>
            <head>
                <title>${docTitle} - ${storeCode}</title>
                ${type === 'zone_barcodes' ? '<script>' + jsBarcodeSource + '<\/script>' : ''}
                <style>
                    body { font-family: sans-serif; padding: 2rem; color: #1e293b; line-height: 1.6; font-size: 13px; }
                    h1 { border-bottom: 2px solid #0f172a; padding-bottom: 0.5rem; font-size: 20px; text-transform: uppercase; }
                    h3 { margin-top: 2rem; border-bottom: 1px solid #cbd5e1; padding-bottom: 0.25rem; font-size: 16px; text-transform: uppercase; }
                    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 2rem; background: #f8fafc; padding: 1rem; border-radius: 8px; border: 1px solid #e2e8f0; }
                    .checklist-table { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 11px; }
                    .checklist-table th { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px; text-align: left; text-transform: uppercase; }
                    .signature-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-top: 3rem; text-align: center; }
                    .sig-box { border-top: 2px solid #cbd5e1; padding-top: 0.5rem; font-weight: bold; }
                </style>
            </head>
            <body ${type === 'zone_barcodes' ? 'onload="JsBarcode(\'.barcode-svg\').init();"' : ''}>
                <h1>Melcom Stock Audit - ${docTitle}</h1>
                <div class="info-grid">
                    <div><strong>Store Code:</strong> ${storeCode}</div>
                    <div><strong>Store Name:</strong> ${storeName}</div>
                    <div><strong>Audit Date:</strong> ${auditStartDate}</div>
                    <div><strong>Counts:</strong> ${countAudit} Audit Staff / ${countShop} Shop Staff / ${countZones} Zones</div>
                </div>
                
                ${contentHTML}
                
                ${type === 'checklist' ? `
                <h3>Declaration</h3>
                <p>We, the undersigned, confirm that the stock take preparation, team allocation, zone configuration, and all mandatory checklists have been completed successfully. The audit is now officially ready to commence.</p>
                ` : ''}
                
                ${type !== 'zone_barcodes' ? `
                <div class="signature-grid">
                    <div class="sig-box">
                        <br><br>
                        Operations Manager<br>
                        <span style="font-weight:normal;">${opsManager}</span>
                    </div>
                    <div class="sig-box">
                        <br><br>
                        Store Manager<br>
                        <span style="font-weight:normal;">${storeManager}</span>
                    </div>
                    <div class="sig-box">
                        <br><br>
                        Audit Lead<br>
                        <span style="font-weight:normal;">${auditLead}</span>
                    </div>
                </div>
                ` : ''}
            </body>
            </html>
        `);
        printWindow.document.close();
        
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
        }, type === 'zone_barcodes' ? 1200 : 500);
    }

    function updateDetailsWizardState() {
        // Toggle card panels
        for (let i = 1; i <= 3; i++) {
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
        const btnNext = document.getElementById("btnDetailsNext");
        const btnNextText = document.getElementById("btnDetailsNextText");
        const btnNextIcon = document.getElementById("btnDetailsNextIcon");
        const btnStartStockTake = document.getElementById("btnStartStockTake");
        const isFinished = localStorage.getItem("melcom_details_finished") === "true";

        if (btnNextText) {
            if (activeDetailsStep === 3) {
                if (isFinished) {
                    btnNextText.innerText = "Finished";
                    if (btnNextIcon) btnNextIcon.style.display = "none";
                    if (btnNext) {
                        btnNext.disabled = true;
                        btnNext.className = "btn btn-disabled";
                    }
                    if (btnStartStockTake) btnStartStockTake.classList.remove("hidden");
                } else {
                    btnNextText.innerText = "Finish";
                    if (btnNextIcon) btnNextIcon.style.display = "none";
                    if (btnNext) {
                        btnNext.disabled = false;
                        btnNext.className = "btn btn-primary";
                    }
                    if (btnStartStockTake) btnStartStockTake.classList.add("hidden");
                }
            } else {
                btnNextText.innerText = "Next";
                if (btnNextIcon) btnNextIcon.style.display = "inline";
                if (btnNext) {
                    btnNext.disabled = false;
                    btnNext.className = "btn btn-primary";
                    
                }
                if (btnStartStockTake) btnStartStockTake.classList.add("hidden");
            }
        }

        updateSidebarDetailsStepNodes();
    }

    function updateSidebarDetailsStepNodes() {
        for (let i = 1; i <= 3; i++) {
            const circle = document.getElementById("circleDetails-" + i);
            const text = document.getElementById("textDetails-" + i);
            if (!circle || !text) continue;

            circle.className = "step-circle";
            text.className = "step-text";

            if (i < activeDetailsStep) {
                circle.classList.add("completed");
                text.classList.add("completed");
                circle.innerHTML = "&#10003;";
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
