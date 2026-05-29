<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - CHECKLIST WORKSPACE VIEW
// Interactive Digital Pre-Stock Take Checklist & Mandatory Report Alerts boards.
// ==========================================================================
?>

<div class="checklist-grid">

    <!-- BOARD 1: PRE-STOCK TAKE CHECKLIST WORKFLOW -->
    <div class="checklist-card">
        <div class="checklist-title-bar">
            <span class="checklist-title">5. Pre-Stock Take Checklist Workflow</span>
            <span id="badge-chkPreStock" class="badge badge-warning">2/5 Done</span>
        </div>

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
                        <td style="font-weight: 700; color: var(--color-text-main);">Setup physical Store Zone Boundaries & Labels</td>
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

    <!-- BOARD 2: MANDATORY REPORT ALERT CHECKLIST -->
    <div class="checklist-card">
        <div class="checklist-title-bar">
            <span class="checklist-title">6. Mandatory Report Alert Checklist</span>
            <span id="badge-chkAlerts" class="badge badge-warning">1/5 Verified</span>
        </div>

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

<!-- ========================================== -->
<!-- CHECKLIST STATE MANAGEMENT -->
<!-- ========================================== -->
<script>
    /**
     * Synchronizes and saves checklists to browser LocalStorage.
     */
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
    }

    /**
     * Loads checklists from LocalStorage.
     */
    function loadChecklistState() {
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
    }

    /**
     * Updates badge counters dynamically.
     */
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

        updateSidebarChecklistTracker(completedPre, verifiedAlert);
    }

    function updateSidebarChecklistTracker(comp, ver) {
        const text = document.getElementById("sidebarChecklistProgressText");
        const fill = document.getElementById("sidebarChecklistProgress");
        if (!text) return;

        const totalDone = comp + ver;
        const percent = Math.round((totalDone / 10) * 100);

        text.innerText = `${totalDone}/10 Completed`;
        if (fill) fill.style.width = `${percent}%`;
    }

    // Auto-run load sequences on Dom content load
    window.addEventListener("DOMContentLoaded", () => {
        loadChecklistState();
    });
</script>
