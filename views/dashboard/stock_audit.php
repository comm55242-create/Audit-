<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - STOCK AUDIT WIZARD VIEW
// Represents the active parameters, scoping, and verification wizard.
// ==========================================================================
?>

<!-- STEP 3: SHOP & DATE SETUP -->
<div id="panelStep-3" class="step-content">
    <div class="workspace-header">
        <h2 class="workspace-title">Shop Setup</h2>
        <p class="workspace-subtitle">Define the active audit parameters and physical credentials</p>
    </div>

    <div class="shop-setup-grid">
        <div class="form-field">
            <label class="form-label">Stock Date</label>
            <input type="date" id="setupDate" class="form-input" value="<?php echo date('Y-m-d'); ?>" required style="height: 38px; padding: 0.375rem 0.75rem;">
        </div>

        <div class="form-field">
            <label class="form-label">Shop Code</label>
            <input type="text" id="setupShopCode" oninput="validateShopCodeField()" class="form-input" placeholder="e.g. SC001" required style="text-transform: uppercase;">
            <div id="shopCodeDescContainer" class="hidden" style="margin-top: 0.5rem; border-radius: 12px; padding: 0.75rem 1rem; font-size: 13px; font-weight: 700; transition: var(--transition-fast); display: flex; align-items: center; gap: 0.5rem;"></div>
        </div>
    </div>
</div>

<!-- STEP 4: AUDIT TYPE -->
<div id="panelStep-4" class="step-content hidden">
    <div class="workspace-header">
        <h2 class="workspace-title">Types</h2>
        <p class="workspace-subtitle">Choose between physical inventory or stock take</p>
    </div>

    <div class="selection-grid">
        <div id="cardPI" class="select-card" onclick="handleTypeCardClick('PI')">
            <input type="checkbox" id="chkTypePI" onclick="event.stopPropagation(); handleTypeCardClick('PI');">
            <div class="select-card-info">
                <span class="select-card-title">PI</span>
                <span class="select-card-desc">Physical Inventory</span>
            </div>
        </div>

        <div id="cardSST" class="select-card" onclick="handleTypeCardClick('SST')">
            <input type="checkbox" id="chkTypeSST" onclick="event.stopPropagation(); handleTypeCardClick('SST');">
            <div class="select-card-info">
                <span class="select-card-title">SST</span>
                <span class="select-card-desc">Shop Stock-Take</span>
            </div>
        </div>
    </div>
</div>

<!-- STEP 5: SCANNING MODE -->
<div id="panelStep-5" class="step-content hidden">
    <div class="workspace-header">
        <h2 class="workspace-title">Modes</h2>
        <p class="workspace-subtitle">Configure scanning behaviors and dynamic item scoping</p>
    </div>

    <div class="selection-grid">
        <!-- ITEMWISE CARD WITH CAPS ON TITLE -->
        <div id="cardItemwise" class="select-card" onclick="handleModeCardClick('ITEMWISE')">
            <input type="checkbox" id="chkModeItemwise" onclick="event.stopPropagation(); handleModeCardClick('ITEMWISE');">
            <div class="select-card-info">
                <span class="select-card-title">ITEMWISE</span>
                <span class="select-card-desc">Specific item configurations</span>
            </div>
        </div>

        <!-- SCANNING CARD WITH CAPS ON TITLE -->
        <div id="cardScanning" class="select-card" onclick="handleModeCardClick('SCANNING')">
            <input type="checkbox" id="chkModeScanning" onclick="event.stopPropagation(); handleModeCardClick('SCANNING');">
            <div class="select-card-info">
                <span class="select-card-title">SCANNING</span>
                <span class="select-card-desc">Continuous barcode scanners</span>
            </div>
        </div>
    </div>
</div>

<!-- STEP 6: DEPARTMENT SELECTOR -->
<div id="panelStep-6" class="step-content hidden">
    <div class="workspace-header">
        <h2 class="workspace-title">Department Selector</h2>
        <p class="workspace-subtitle">Add active departments or search filter groups</p>
    </div>

    <!-- Search Input & Auto-Select toggle section -->
    <div style="display: flex; gap: 1rem; align-items: center; width: 100%; max-width: 1152px; margin-bottom: 0.75rem;">
        <div class="input-icon-wrapper" style="flex: 1;">
            <span class="input-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" id="searchDepartmentsInput" class="form-input has-icon" placeholder="Quick search department codes..." oninput="handleSearchDepartmentsInput(this)">
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; background-color: #f8fafc; border: 1px solid var(--color-border); border-radius: 12px; padding: 0.5rem 0.875rem;">
            <span style="font-size: 10px; font-weight: 800; color: var(--color-text-main); letter-spacing: 0.025em; text-transform: uppercase;">AUTO-SELECT GROUPS & SUBGROUPS</span>
            <label class="switch">
                <input type="checkbox" id="toggleAutoSelectAllChildren">
                <span class="slider"></span>
            </label>
        </div>
    </div>

    <div class="tree-container" id="deptsTreeContainer">
        <!-- Javascript renders department tree boxes dynamically -->
    </div>
</div>

<!-- STEP 7: GROUPS & SUBGROUPS SELECTION (SST Only) -->
<div id="panelStep-7" class="step-content hidden">
    <div class="workspace-header">
        <h2 class="workspace-title">Groups & Subgroups</h2>
        <p class="workspace-subtitle">Filter specific scoping categories for SST parameters</p>
    </div>

    <!-- (Scoping Depth toggle removed) -->

    <!-- Main dynamic categorization grids (Visible only when scoping depth is segment-specific) -->
    <div id="sstCategoriesGrid" class="tree-container" style="max-height: 380px;">
        
        <!-- Column 1: Groups Checklist -->
        <div class="tree-column">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.25rem; border-bottom: 1px solid var(--color-border);">
                <span class="tree-column-header" style="margin-bottom: 0;">Active Groups Selection</span>
                <div style="display: flex; gap: 0.35rem;">
                    <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 10px; font-weight: 800; border-radius: 6px; cursor: pointer; height: auto;" onclick="toggleAllGroups(true)">Select All</button>
                    <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 10px; font-weight: 800; border-radius: 6px; cursor: pointer; height: auto;" onclick="toggleAllGroups(false)">Deselect All</button>
                </div>
            </div>
            <div id="groupsColumnList" class="tree-column-list">
                <span class="tree-node-text disabled-msg">Select departments in Step 6 to populate groups.</span>
            </div>
        </div>

        <!-- Column 2: Subgroups Checklist -->
        <div class="tree-column" style="grid-column: span 2;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.25rem; border-bottom: 1px solid var(--color-border);">
                <span class="tree-column-header" style="margin-bottom: 0;">Active Subgroups Selection</span>
                <div style="display: flex; gap: 0.35rem;">
                    <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 10px; font-weight: 800; border-radius: 6px; cursor: pointer; height: auto;" onclick="toggleAllSubgroups(true)">Select All</button>
                    <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 10px; font-weight: 800; border-radius: 6px; cursor: pointer; height: auto;" onclick="toggleAllSubgroups(false)">Deselect All</button>
                </div>
            </div>
            <div id="subgroupsColumnList" class="tree-column-list">
                <span class="tree-node-text disabled-msg">Select active groups to populate subgroups.</span>
            </div>
        </div>

    </div>
</div>

<!-- STEP 8: SUMMARY & EXPORT -->
<div id="panelStep-8" class="step-content hidden">
    <div class="workspace-header">
        <h2 class="workspace-title">Summary & Export</h2>
        <p class="workspace-subtitle">Confirm parameters structured inside the initialization table</p>
    </div>

    <!-- Inline Success Message Banner -->
    <div id="setupSuccessMessage" class="success-banner hidden">
        <svg style="width:16px;height:16px;color:#064e3b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
        <span>Successfully Submitted</span>
    </div>

    <!-- Unified HTML Table in full tabular form with column headers at the top -->
    <div class="table-wrapper">
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Stock Date</th>
                    <th>Shop Code</th>
                    <th>Audit Type</th>
                    <th>Mode</th>
                    <th>Department</th>
                    <th>Group</th>
                    <th>Subgroup</th>
                </tr>
            </thead>
            <tbody id="summaryTableBody">
                <!-- Javascript inserts rows dynamically -->
            </tbody>
        </table>
    </div>
</div>

<!-- CARD FOOTER ACTIONS PANEL -->
<div class="workspace-footer">
    <!-- Back Button -->
    <button type="button" id="btnGlobalBack" onclick="handleNavigationBack()" class="btn btn-secondary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        <span>Back</span>
    </button>

    <div style="display: flex; align-items: center;">
        <!-- Export CSV Button (Visible on Step 8 only after submit) -->
        <button type="button" id="btnExportCsv" onclick="exportSetupToCsv()" class="btn btn-secondary hidden" style="margin-right: 1rem; border-color: var(--color-success); color: var(--color-success); display: inline-flex; align-items: center; gap: 0.35rem; height: auto;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0L8 8m4-4v12"/></svg>
            <span>Export CSV</span>
        </button>

        <!-- Print Setup Button (Visible on Step 8 only after submit) -->
        <button type="button" id="btnPrintSetup" onclick="printSetupSheet()" class="btn btn-secondary hidden" style="margin-right: 1rem;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Print Setup</span>
        </button>

        <!-- Next / Continue Button -->
        <button type="button" id="btnGlobalNext" onclick="handleNavigationNext()" class="btn btn-primary">
            <span id="btnGlobalNextText">Next</span>
            <svg id="btnGlobalNextIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>
</div>

<!-- ============================================== -->
<!-- POPUPS, DIALOGS, MODALS -->
<!-- ============================================== -->

<!-- Premium Custom Confirmation Alert Dialog modal (Wide review layout) -->
<div id="confirmSubmitModal" class="modal-backdrop hidden">
    <div class="modal-card-fullscreen">
        <div style="display: flex; align-items: center; gap: 1rem; border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem; margin-bottom: 0.25rem;">
            <div class="modal-icon-circle" style="width: 42px; height: 42px; font-size: 20px; background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; margin: 0;">?</div>
            <div style="display: flex; flex-direction: column;">
                <h3 class="modal-title" style="font-size: 1.125rem;">Review & Verify Setup</h3>
                <p class="modal-desc" style="font-size: 12px; margin: 0;">Please review your selected audit configuration scope before submitting.</p>
            </div>
            <!-- Premium Forest-Green View Detailed Report Button in header -->
            <button type="button" onclick="printSetupSheet(false)" class="btn btn-secondary" style="margin-left: auto; padding: 0.5rem 1rem; font-size: 11px; font-weight: 800; border-radius: 10px; display: inline-flex; align-items: center; gap: 0.35rem; height: auto;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke-width: 2.5; color: var(--color-primary);"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>View Detailed Report</span>
            </button>
        </div>
        
        <!-- Table Preview Grid inside Modal -->
        <div class="modal-table-wrapper" style="margin-top: 0.25rem;">
            <table class="summary-table" style="margin: 0; width: 100%;">
                <thead>
                    <tr>
                        <th>Stock Date</th>
                        <th>Shop Code</th>
                        <th>Audit Type</th>
                        <th>Mode</th>
                        <th>Department</th>
                        <th>Group</th>
                        <th>Subgroup</th>
                    </tr>
                </thead>
                <tbody id="modalSummaryTableBody">
                    <!-- Cloned rows inserted here -->
                </tbody>
            </table>
        </div>

        <!-- Verification toggle with user-requested feedback implemented -->
        <div class="verification-box">
            <label class="switch">
                <input type="checkbox" id="verificationApproveToggle" onchange="handleVerificationApprovalChange()">
                <span class="slider"></span>
            </label>
            <div class="verification-text-stack">
                <span class="verification-title">Verify Configuration</span>
                <span class="verification-desc">I hereby confirm that all selected audit scopes, physical credentials, departments, groups, and subgroups have been thoroughly checked and are 100% correct.</span>
            </div>
        </div>

        <!-- Side-by-side action buttons -->
        <div style="display: flex; gap: 1rem; width: 100%; margin-top: 0.5rem; justify-content: flex-end;">
            <button type="button" onclick="closeConfirmModal()" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 700; font-size: 12px; cursor: pointer; height: auto;">Cancel</button>
            <button type="button" id="btnFinalDbSubmit" onclick="executeFinalSubmit()" class="btn btn-disabled" disabled style="padding: 0.75rem 2rem; border-radius: 12px; font-weight: 700; font-size: 12px; cursor: pointer; height: auto;">Submit</button>
        </div>
    </div>
</div>

<!-- Premium Loader Modal -->
<div id="loadingModal" class="modal-backdrop hidden">
    <div class="modal-card" style="max-width: 360px; padding: 2.25rem 2rem; border-radius: 20px;">
        <div class="spinner-container" style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
            <div class="loading-spinner" style="width: 48px; height: 48px; border-width: 4.5px;"></div>
        </div>
        <div class="modal-info-stack" style="text-align: center;">
            <h3 id="loadingTitle" class="modal-title" style="font-size: 1.15rem; font-weight: 900; color: var(--color-text-main); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: -0.01em;">Compiling Scope...</h3>
            <p id="loadingDesc" class="modal-desc" style="font-size: 12px; color: var(--color-text-muted); line-height: 1.5; margin: 0;">Please wait while we structure your database-wide audit configurations. This may take a moment.</p>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- ACTIVE WIZARD ENGINE SCRIPTS -->
<!-- ============================================== -->
<script>
    const deptsData = <?php
    if ($db_status === 'ONLINE' && !empty($db_depts)) {
        $jsDepts = [];
        $deptCounter = 1;
        
        foreach ($db_depts as $deptName) {
            $deptId = 'D' . $deptCounter;
            $deptGroups = [];
            $groupCounter = 1;
            
            if (!empty($db_groups)) {
                foreach ($db_groups as $groupName) {
                    $groupId = 'G' . $deptCounter . '_' . $groupCounter;
                    
                    $subList = [];
                    if (isset($db_subgroups[$groupName])) {
                        $subCounter = 1;
                        foreach ($db_subgroups[$groupName] as $subName) {
                            $subList[] = [
                                'id' => 'S' . $deptCounter . '_' . $groupCounter . '_' . $subCounter,
                                'name' => $subName
                            ];
                            $subCounter++;
                        }
                    }
                    
                    if (empty($subList)) {
                        $subList[] = [
                            'id' => 'S' . $deptCounter . '_' . $groupCounter . '_1',
                            'name' => 'All Subgroups'
                        ];
                    }
                    
                    $deptGroups[] = [
                        'id' => $groupId,
                        'name' => $groupName,
                        'subgroups' => $subList
                    ];
                    $groupCounter++;
                }
            }
            
            $jsDepts[] = [
                'id' => $deptId,
                'name' => $deptName,
                'groups' => $deptGroups
            ];
            $deptCounter++;
        }
        echo json_encode($jsDepts);
    } else {
        // High fidelity offline-ready categories fallback
        $mockDepts = [
            [
                'id' => 'D1',
                'name' => 'Groceries & Provisions',
                'groups' => [
                    [
                        'id' => 'G1',
                        'name' => 'Beverages',
                        'subgroups' => [
                            ['id' => 'S1', 'name' => 'Soft Drinks'],
                            ['id' => 'S2', 'name' => 'Fruit Juices'],
                            ['id' => 'S3', 'name' => 'Energy Drinks']
                        ]
                    ],
                    [
                        'id' => 'G2',
                        'name' => 'Canned Foods',
                        'subgroups' => [
                            ['id' => 'S4', 'name' => 'Canned Fish'],
                            ['id' => 'S5', 'name' => 'Canned Vegetables']
                        ]
                    ]
                ]
            ],
            [
                'id' => 'D2',
                'name' => 'Electronics & Tech',
                'groups' => [
                    [
                        'id' => 'G3',
                        'name' => 'Computers',
                        'subgroups' => [
                            ['id' => 'S6', 'name' => 'Laptops'],
                            ['id' => 'S7', 'name' => 'Desktop PCs']
                        ]
                    ],
                    [
                        'id' => 'G4',
                        'name' => 'Home Appliances',
                        'subgroups' => [
                            ['id' => 'S8', 'name' => 'Refrigerators'],
                            ['id' => 'S9', 'name' => 'Microwaves']
                        ]
                    ]
                ]
            ],
            [
                'id' => 'D3',
                'name' => 'Apparel & Fashion',
                'groups' => [
                    [
                        'id' => 'G5',
                        'name' => 'Menswear',
                        'subgroups' => [
                            ['id' => 'S10', 'name' => 'Shirts & T-shirts'],
                            ['id' => 'S11', 'name' => 'Trousers & Jeans']
                        ]
                    ],
                    [
                        'id' => 'G6',
                        'name' => 'Footwear',
                        'subgroups' => [
                            ['id' => 'S12', 'name' => 'Running Sneakers'],
                            ['id' => 'S13', 'name' => 'Formal Leather Shoes']
                        ]
                    ]
                ]
            ]
        ];
        echo json_encode($mockDepts);
    }
    ?>;

    let activeStep = 3; // Starts directly at Shop Setup (Step 3) since authenticated
    let selectedType = "";
    let selectedMode = [];
    let selectedScopeDepth = "Dept";
    let isSetupSubmitted = localStorage.getItem("melcom_stock_audit_submitted") === "true";
    let selectedItemwiseItems = [];
    let isShopCodeConfirmed = false;
    let shopLookupTimeout = null;

    window.addEventListener("DOMContentLoaded", () => {
        renderCheckboxDepts();
        updateWizardState();
    });

    /**
     * Displays the premium full-screen loading overlay with custom messages.
     */
    function showLoader(title, description) {
        const loader = document.getElementById("loadingModal");
        const titleEl = document.getElementById("loadingTitle");
        const descEl = document.getElementById("loadingDesc");
        
        if (titleEl) titleEl.innerText = title;
        if (descEl) descEl.innerText = description;
        if (loader) loader.classList.remove("hidden");
    }

    /**
     * Dismisses the premium loading overlay.
     */
    function hideLoader() {
        const loader = document.getElementById("loadingModal");
        if (loader) loader.classList.add("hidden");
    }

    // -------------------------------------------------------------
    // CORE NAVIGATION CONTROLLER
    // -------------------------------------------------------------
    function updateWizardState() {
        // Hide all active wizard panels (Steps 3-8)
        for (let i = 3; i <= 8; i++) {
            const el = document.getElementById("panelStep-" + i);
            if (el) el.classList.add("hidden");
        }
        
        // Show active panel
        const activePanel = document.getElementById("panelStep-" + activeStep);
        if (activePanel) activePanel.classList.remove("hidden");

        // Update sidebar progress states
        updateSidebar(activeStep);

        // Fetch core actions elements
        const btnBack = document.getElementById("btnGlobalBack");
        const btnNext = document.getElementById("btnGlobalNext");
        const btnNextText = document.getElementById("btnGlobalNextText");
        const btnNextIcon = document.getElementById("btnGlobalNextIcon");
        const btnExport = document.getElementById("btnExportCsv");
        const btnPrint = document.getElementById("btnPrintSetup");
        const successMsg = document.getElementById("setupSuccessMessage");

        // Back button visibility (hidden on starting Step 3 and completed Step 8)
        if (activeStep === 3 || (activeStep === 8 && isSetupSubmitted)) {
            btnBack.style.visibility = "hidden";
        } else {
            btnBack.style.visibility = "visible";
        }

        // Configure Step 8 action bars
        if (activeStep === 8) {
            if (isSetupSubmitted) {
                btnExport.classList.remove("hidden");
                btnPrint.classList.remove("hidden");
                if (successMsg) successMsg.classList.remove("hidden");
                
                btnNextText.innerText = "Setup Saved";
                btnNextIcon.innerHTML = `<svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
                btnNext.className = "btn badge-saved";
                btnNext.disabled = true;
            } else {
                btnExport.classList.add("hidden");
                btnPrint.classList.add("hidden");
                if (successMsg) successMsg.classList.add("hidden");

                btnNextText.innerText = "Continue";
                btnNextIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>`;
                btnNext.className = "btn btn-primary";
                btnNext.disabled = false;
            }
        } else {
            btnExport.classList.add("hidden");
            btnPrint.classList.add("hidden");
            if (successMsg) successMsg.classList.add("hidden");

            btnNextText.innerText = "Next";
            btnNextIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>`;
            validateActiveStepForm();
        }
    }

    function updateSidebar(step) {
        const totalSteps = 8;
        const isPi = (selectedType === 'PI');
        
        // Hide Step 7 if in Complete PI, or Shop Stock-Take SST scoping
        const hideStep7 = (isPi || (selectedType === 'SST' && selectedScopeDepth === 'SST'));
        
        let completedSteps = step - 1;
        if (isPi && step > 5) {
            completedSteps = step === 8 ? 5 : step - 1;
        } else if (hideStep7 && step > 6) {
            completedSteps = step === 8 ? 6 : step - 1;
        }
        
        const divisor = isPi ? 5 : (hideStep7 ? 7 : 8);
        const progressPercent = Math.round((completedSteps / divisor) * 100);
        
        const barFill = document.getElementById("sidebarProgress");
        const barText = document.getElementById("sidebarProgressText");
        if (barFill) barFill.style.width = `${progressPercent}%`;
        if (barText) barText.innerText = `${completedSteps}/${divisor} completed`;

        for (let i = 3; i <= totalSteps; i++) {
            const stepMenu = document.getElementById("menuStep-" + i);
            const circle = document.getElementById("circle-" + i);
            const text = document.getElementById("text-" + i);
            
            if (!stepMenu || !circle || !text) continue;

            // Manage Steps visibility under PI mode
            if (isPi && (i === 6 || i === 7)) {
                stepMenu.classList.add("hidden");
                continue;
            } else if (i === 7 && hideStep7) {
                stepMenu.classList.add("hidden");
                continue;
            } else {
                stepMenu.classList.remove("hidden");
            }

            // Update CSS state classes
            if (i < step) {
                circle.className = "step-circle completed";
                circle.innerText = "✓";
                text.className = "step-text completed";
            } else if (i === step) {
                circle.className = "step-circle active";
                circle.innerText = i;
                text.className = "step-text active";
            } else {
                circle.className = "step-circle upcoming";
                circle.innerText = i;
                text.className = "step-text upcoming";
            }
        }
    }

    function handleNavigationNext() {
        if (activeStep === 8) {
            showConfirmModal();
            return;
        }

        let targetStep = activeStep;

        if (activeStep === 5) {
            if (selectedType === 'PI') {
                targetStep = 8; // Complete PI skips Steps 6 & 7
            } else {
                targetStep = 6;
            }
        } else if (activeStep === 6 && selectedScopeDepth === 'SST') {
            targetStep = 8; // SST depth skips Step 7 (Groups & Subgroups)
        } else {
            targetStep++;
        }
        
        if (targetStep === 8) {
            showLoader(
                "Compiling Scope...",
                "Please wait while we structure and pull your selected departments, groups, and items into the master initialization table. This may take a moment."
            );
            
            setTimeout(() => {
                activeStep = targetStep;
                compileSummaryData();
                updateWizardState();
                hideLoader();
            }, 800); // 800ms timeout to allow smooth visual compiling state render
        } else {
            activeStep = targetStep;
            updateWizardState();
        }
    }

    function handleNavigationBack() {
        if (activeStep === 3) return; // Prevent going back to authentication views

        let targetStep = activeStep;

        if (activeStep === 8) {
            if (selectedType === 'PI') {
                targetStep = 5;
            } else if (selectedScopeDepth === 'SST') {
                targetStep = 6;
            } else {
                targetStep = 7;
            }
        } else if (activeStep === 6) {
            targetStep = 5;
        } else {
            targetStep--;
        }

        activeStep = targetStep;
        updateWizardState();
    }

    // -------------------------------------------------------------
    // PARAMETERS SELECTION CHANGE ENGINE HANDLERS
    // -------------------------------------------------------------
    function validateShopCodeField() {
        const input = document.getElementById("setupShopCode");
        input.value = input.value.toUpperCase();
        
        const code = input.value.trim();
        const descContainer = document.getElementById("shopCodeDescContainer");
        
        // Clear any active debounce timer
        if (shopLookupTimeout) clearTimeout(shopLookupTimeout);
        
        if (code.length < 2) {
            isShopCodeConfirmed = false;
            descContainer.classList.add("hidden");
            descContainer.style.display = "none";
            validateActiveStepForm();
            return;
        }
        
        // Show "checking..." state in descContainer
        descContainer.classList.remove("hidden");
        descContainer.style.display = "flex";
        descContainer.style.background = "#f8fafc";
        descContainer.style.border = "1px solid #e2e8f0";
        descContainer.style.color = "#64748b";
        descContainer.innerHTML = `
            <svg class="animate-spin" style="width: 16px; height: 16px; color: #64748b; flex-shrink: 0; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18" />
            </svg>
            <span>Checking Shop Code...</span>
        `;
        
        // Disable next button immediately during lookup to prevent bypass
        document.getElementById("btnGlobalNext").disabled = true;
        isShopCodeConfirmed = false;
        
        // Debounce lookup by 400ms to avoid DB load during quick typing
        shopLookupTimeout = setTimeout(() => {
            fetch(`index.php?route=audit/shop-lookup&shop_code=${encodeURIComponent(code)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    isShopCodeConfirmed = true;
                    descContainer.style.background = "#ecfdf5";
                    descContainer.style.border = "1px solid #a7f3d0";
                    descContainer.style.color = "#065f46";
                    descContainer.innerHTML = `
                        <svg style="width: 16px; height: 16px; color: #10b981; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span style="letter-spacing: 0.02em;">${data.shop_desc}</span>
                    `;
                } else {
                    isShopCodeConfirmed = false;
                    descContainer.style.background = "#fef2f2";
                    descContainer.style.border = "1px solid #fecaca";
                    descContainer.style.color = "#991b1b";
                    descContainer.innerHTML = `
                        <svg style="width: 16px; height: 16px; color: #ef4444; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Shop Code not registered. Please verify code.</span>
                    `;
                }
                validateActiveStepForm();
            })
            .catch(err => {
                isShopCodeConfirmed = false;
                descContainer.style.background = "#fef2f2";
                descContainer.style.border = "1px solid #fecaca";
                descContainer.style.color = "#991b1b";
                descContainer.innerHTML = `
                    <svg style="width: 16px; height: 16px; color: #ef4444; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Connection error: Could not query MST_SHOP.</span>
                `;
                validateActiveStepForm();
            });
        }, 400);
    }

    function handleTypeCardClick(type) {
        selectedType = type;
        document.getElementById("chkTypePI").checked = (type === 'PI');
        document.getElementById("chkTypeSST").checked = (type === 'SST');

        document.getElementById("cardPI").className = type === 'PI' ? "select-card selected" : "select-card";
        document.getElementById("cardSST").className = type === 'SST' ? "select-card selected" : "select-card";

        // Reset scanning modes & scoping depths
        selectedMode = [];
        document.getElementById("chkModeItemwise").checked = false;
        document.getElementById("chkModeScanning").checked = false;
        document.getElementById("cardItemwise").className = "select-card";
        document.getElementById("cardScanning").className = "select-card";
        selectedItemwiseItems = [];

        validateActiveStepForm();
    }

    function handleModeCardClick(mode) {
        const chkItemwise = document.getElementById("chkModeItemwise");
        const chkScanning = document.getElementById("chkModeScanning");
        
        if (mode === 'ITEMWISE') {
            chkItemwise.checked = !chkItemwise.checked;
            chkScanning.checked = false;
        } else {
            chkScanning.checked = !chkScanning.checked;
            chkItemwise.checked = false;
        }

        document.getElementById("cardItemwise").className = chkItemwise.checked ? "select-card selected" : "select-card";
        document.getElementById("cardScanning").className = chkScanning.checked ? "select-card selected" : "select-card";

        selectedMode = [];
        if (chkItemwise.checked) selectedMode.push("Itemwise");
        if (chkScanning.checked) selectedMode.push("Scanning");

        validateActiveStepForm();
    }

    function toggleAllGroups(checked) {
        const checkboxes = document.querySelectorAll(".group-node");
        checkboxes.forEach(chk => {
            chk.checked = checked;
        });
        renderSubgroupsSegmentColumnList();
        validateActiveStepForm();
    }

    function toggleAllSubgroups(checked) {
        const checkboxes = document.querySelectorAll(".subgroup-node");
        checkboxes.forEach(chk => {
            chk.checked = checked;
        });
        validateActiveStepForm();
    }

    // -------------------------------------------------------------
    // DYNAMIC AJAX SEARCH & LOOKUP ENGINE (OCI BINDINGS MAPPED)
    // -------------------------------------------------------------
    function handleSearchInputKeyDown(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            executeItemSearch();
        }
    }

    function executeItemSearch() {
        const val = document.getElementById("itemQueryInput").value.trim();
        const tbody = document.getElementById("itemsSearchResultTableBody");
        if (val === "") {
            tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: var(--color-text-light); font-size: 11px; padding: 1.5rem 0;">Type search query above to populate database matching items list.</td></tr>`;
            return;
        }

        tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; font-size: 11px; padding: 1.5rem 0;"><div class="loading-spinner" style="width: 24px; height: 24px; border-width: 3px; margin: 0 auto;"></div></td></tr>`;

        // Direct fetch to refactored MVC endpoint
        fetch(`index.php?route=audit/search&query=${encodeURIComponent(val)}`)
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: var(--color-danger); font-size: 11px; padding: 1.5rem 0;">No matching items found. Please verify query inputs.</td></tr>`;
                return;
            }

            let html = "";
            data.forEach(item => {
                const isChecked = selectedItemwiseItems.some(i => i.ITEM_CODE === item.ITEM_CODE);
                html += `
                    <tr class="${isChecked ? 'row-highlight' : ''}">
                        <td style="text-align: center; padding: 0.5rem 1rem;">
                            <input type="checkbox" class="itemwise-node" value="${item.ITEM_CODE}" data-name="${item.ITEM_NAME}" data-barcode="${item.BARCODE}" data-price="${item.PRICE}" data-stock="${item.CURR_STOCK}" data-dept="${item.DEPT}" data-group="${item.VC_GROUP}" data-subgroup="${item.VC_SUBGROUP}" onchange="handleItemwiseRowCheckboxChange(this)" ${isChecked ? 'checked' : ''} style="width: 15px; height: 15px; accent-color: var(--color-primary);">
                        </td>
                        <td style="padding: 0.5rem 1rem;">${item.ITEM_CODE}</td>
                        <td style="padding: 0.5rem 1rem;">${item.ITEM_NAME}</td>
                        <td style="padding: 0.5rem 1rem;">${item.BARCODE}</td>
                        <td style="padding: 0.5rem 1rem;">$${item.PRICE.toFixed(2)}</td>
                        <td style="padding: 0.5rem 1rem;">${item.CURR_STOCK}</td>
                        <td style="padding: 0.5rem 1rem;">${item.DEPT}</td>
                        <td style="padding: 0.5rem 1rem;">${item.VC_GROUP}</td>
                        <td style="padding: 0.5rem 1rem;">${item.VC_SUBGROUP}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
            syncSelectAllItemsToggleState();
        })
        .catch(err => {
            tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: var(--color-danger); font-size: 11px; padding: 1.5rem 0;">Batch lookup failed. Ensure active Oracle server.</td></tr>`;
        });
    }

    function handleItemwiseRowCheckboxChange(chk) {
        const itemCode = chk.value;
        const tr = chk.closest("tr");
        
        if (chk.checked) {
            tr.classList.add("row-highlight");
            const item = {
                ITEM_CODE: itemCode,
                ITEM_NAME: chk.getAttribute("data-name"),
                BARCODE: chk.getAttribute("data-barcode"),
                PRICE: parseFloat(chk.getAttribute("data-price")),
                CURR_STOCK: parseInt(chk.getAttribute("data-stock")),
                DEPT: chk.getAttribute("data-dept"),
                VC_GROUP: chk.getAttribute("data-group"),
                VC_SUBGROUP: chk.getAttribute("data-subgroup")
            };
            if (!selectedItemwiseItems.some(i => i.ITEM_CODE === itemCode)) {
                selectedItemwiseItems.push(item);
            }
        } else {
            tr.classList.remove("row-highlight");
            selectedItemwiseItems = selectedItemwiseItems.filter(i => i.ITEM_CODE !== itemCode);
        }
        syncSelectAllItemsToggleState();
        validateActiveStepForm();
    }

    function handleSelectAllItemsToggleChange(master) {
        const checkboxes = document.querySelectorAll(".itemwise-node");
        checkboxes.forEach(chk => {
            if (chk.checked !== master.checked) {
                chk.checked = master.checked;
                handleItemwiseRowCheckboxChange(chk);
            }
        });
    }

    function syncSelectAllItemsToggleState() {
        const checkboxes = document.querySelectorAll(".itemwise-node");
        const master = document.getElementById("toggleSelectAllItems");
        if (!master) return;

        if (checkboxes.length === 0) {
            master.checked = false;
            return;
        }

        let allChecked = true;
        checkboxes.forEach(chk => {
            if (!chk.checked) allChecked = false;
        });
        master.checked = allChecked;
    }

    // -------------------------------------------------------------
    // DYNAMIC CLIENT CSV IMPORT ENGINE
    // -------------------------------------------------------------
    function triggerCsvImport() {
        document.getElementById("csvFileInput").click();
    }

    function parseItemCsvFile(event) {
        const file = event.target.files[0];
        const input = event.target;
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            const lines = text.split(/\r?\n/);
            const codes = [];

            lines.forEach(line => {
                const cols = line.split(/[;,]/);
                cols.forEach(val => {
                    const clean = val.trim().replace(/['"']/g, '');
                    if (clean.length > 0) {
                        codes.push(clean);
                    }
                });
            });

            if (codes.length === 0) {
                alert("The CSV file appears empty. Verify formatting.");
                input.value = "";
                return;
            }

            const tbody = document.getElementById("itemsSearchResultTableBody");
            tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; font-size: 11px; padding: 1.5rem 0;">Batch importing ${codes.length} CSV scopes...</td></tr>`;

            // POST CSV batch directly to dynamic MVC lookup
            fetch("index.php?route=audit/lookup", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ codes: codes })
            })
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: var(--color-danger); font-size: 11px; padding: 1.5rem 0;">No matching items found in the database. Ensure valid item codes.</td></tr>`;
                    input.value = "";
                    return;
                }

                // Batch append
                data.forEach(item => {
                    if (!selectedItemwiseItems.some(i => i.ITEM_CODE === item.ITEM_CODE)) {
                        selectedItemwiseItems.push(item);
                    }
                });

                let html = "";
                data.forEach(item => {
                    html += `
                        <tr class="row-highlight">
                            <td style="text-align: center; padding: 0.5rem 1rem;">
                                <input type="checkbox" class="itemwise-node" value="${item.ITEM_CODE}" data-name="${item.ITEM_NAME}" data-barcode="${item.BARCODE}" data-price="${item.PRICE}" data-stock="${item.CURR_STOCK}" data-dept="${item.DEPT}" data-group="${item.VC_GROUP}" data-subgroup="${item.VC_SUBGROUP}" onchange="handleItemwiseRowCheckboxChange(this)" checked style="width: 15px; height: 15px; accent-color: var(--color-primary);">
                            </td>
                            <td style="padding: 0.5rem 1rem;">${item.ITEM_CODE}</td>
                            <td style="padding: 0.5rem 1rem;">${item.ITEM_NAME}</td>
                            <td style="padding: 0.5rem 1rem;">${item.BARCODE}</td>
                            <td style="padding: 0.5rem 1rem;">$${item.PRICE.toFixed(2)}</td>
                            <td style="padding: 0.5rem 1rem;">${item.CURR_STOCK}</td>
                            <td style="padding: 0.5rem 1rem;">${item.DEPT}</td>
                            <td style="padding: 0.5rem 1rem;">${item.VC_GROUP}</td>
                            <td style="padding: 0.5rem 1rem;">${item.VC_SUBGROUP}</td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
                const masterSelectAll = document.getElementById("toggleSelectAllItems");
                if (masterSelectAll) masterSelectAll.checked = true;

                alert(`Successfully imported ${data.length} items from CSV into active scope!`);
                input.value = "";
                validateActiveStepForm();
            })
            .catch(err => {
                tbody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: var(--color-danger); font-size: 11px; padding: 1.5rem 0;">Batch lookup failed. Ensure active server.</td></tr>`;
                input.value = "";
            });
        };
        
        reader.readAsText(file);
    }

    // -------------------------------------------------------------
    // DYNAMIC CATEGORY RENDER ENGINE (STEPS 6 & 7)
    // -------------------------------------------------------------
    function renderCheckboxDepts() {
        const container = document.getElementById("deptsTreeContainer");
        if (!container) return;

        let html = "";
        deptsData.forEach(dept => {
            html += `
                <div class="tree-column dept-tree-block" data-name="${dept.name.toUpperCase()}">
                    <label class="tree-node-label" style="background-color: #f8fafc; border-color: var(--color-border);">
                        <input type="checkbox" class="dept-node" value="${dept.id}" data-name="${dept.name}" onchange="handleDeptCheckboxChange(this)">
                        <span class="tree-node-text" style="font-weight: 800; color: var(--color-text-main); font-size: 13px;">${dept.name}</span>
                    </label>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function handleSearchDepartmentsInput(input) {
        const q = input.value.trim().toUpperCase();
        const blocks = document.querySelectorAll(".dept-tree-block");
        blocks.forEach(b => {
            const name = b.getAttribute("data-name");
            if (name.includes(q)) {
                b.style.display = "flex";
            } else {
                b.style.display = "none";
            }
        });
    }

    function handleDeptCheckboxChange(chk) {
        const deptId = chk.value;
        const autoSelect = document.getElementById("toggleAutoSelectAllChildren").checked;

        if (autoSelect) {
            // Auto check all groups and subgroups under this department
            const dept = deptsData.find(d => d.id === deptId);
            if (dept && chk.checked) {
                dept.groups.forEach(g => {
                    // Check if group is already checked in segment column
                    renderGroupsSegmentColumnList();
                });
            }
        }

        renderGroupsSegmentColumnList();
        validateActiveStepForm();
    }

    function renderGroupsSegmentColumnList() {
        const container = document.getElementById("groupsColumnList");
        if (!container) return;

        const checkedDepts = document.querySelectorAll(".dept-node:checked");
        if (checkedDepts.length === 0) {
            container.innerHTML = `<span class="tree-node-text disabled-msg">Select departments in Step 6 to populate groups.</span>`;
            document.getElementById("subgroupsColumnList").innerHTML = `<span class="tree-node-text disabled-msg">Select active groups to populate subgroups.</span>`;
            return;
        }

        const autoSelect = document.getElementById("toggleAutoSelectAllChildren").checked;
        let html = "";

        checkedDepts.forEach(dNode => {
            const deptId = dNode.value;
            const dept = deptsData.find(d => d.id === deptId);
            
            if (dept) {
                dept.groups.forEach(group => {
                    const isChecked = autoSelect;
                    html += `
                        <label class="tree-node-label">
                            <input type="checkbox" class="group-node" value="${group.id}" data-deptid="${deptId}" data-name="${group.name}" onchange="handleGroupCheckboxChange(this)" ${isChecked ? 'checked' : ''}>
                            <span class="tree-node-text">${group.name}</span>
                        </label>
                    `;
                });
            }
        });

        container.innerHTML = html;
        renderSubgroupsSegmentColumnList();
    }

    function handleGroupCheckboxChange(chk) {
        renderSubgroupsSegmentColumnList();
        validateActiveStepForm();
    }

    function renderSubgroupsSegmentColumnList() {
        const container = document.getElementById("subgroupsColumnList");
        if (!container) return;

        const checkedGroups = document.querySelectorAll(".group-node:checked");
        if (checkedGroups.length === 0) {
            container.innerHTML = `<span class="tree-node-text disabled-msg">Select active groups to populate subgroups.</span>`;
            return;
        }

        const autoSelect = document.getElementById("toggleAutoSelectAllChildren").checked;
        let html = "";

        checkedGroups.forEach(gNode => {
            const groupId = gNode.value;
            const deptId = gNode.getAttribute("data-deptid");
            const dept = deptsData.find(d => d.id === deptId);
            
            if (dept) {
                const group = dept.groups.find(g => g.id === groupId);
                if (group) {
                    group.subgroups.forEach(sub => {
                        const isChecked = autoSelect;
                        html += `
                            <label class="tree-node-label">
                                <input type="checkbox" class="subgroup-node" value="${sub.id}" data-groupid="${groupId}" data-name="${sub.name}" onchange="validateActiveStepForm()" ${isChecked ? 'checked' : ''}>
                                <span class="tree-node-text">${sub.name}</span>
                            </label>
                        `;
                    });
                }
            }
        });

        container.innerHTML = html;
    }

    // -------------------------------------------------------------
    // DYNAMIC WIZARD STEP VALIDATION
    // -------------------------------------------------------------
    function validateActiveStepForm() {
        const btnNext = document.getElementById("btnGlobalNext");
        let isValid = false;

        if (activeStep === 3) {
            isValid = isShopCodeConfirmed;
        } else if (activeStep === 4) {
            isValid = (selectedType !== "");
        } else if (activeStep === 5) {
            const chkItemwise = document.getElementById("chkModeItemwise").checked;
            const chkScanning = document.getElementById("chkModeScanning").checked;

            if (chkItemwise) {
                isValid = true;
            } else if (chkScanning) {
                isValid = false; // Prevents progression for SCANNING mode as requested
            } else {
                isValid = false;
            }
        } else if (activeStep === 6) {
            const checkedDepts = document.querySelectorAll(".dept-node:checked");
            isValid = (checkedDepts.length > 0);
        } else if (activeStep === 7) {
            if (selectedScopeDepth === 'SST') {
                isValid = true;
            } else {
                const checkedGroups = document.querySelectorAll(".group-node:checked");
                isValid = (checkedGroups.length > 0);
            }
        }

        if (btnNext) btnNext.disabled = !isValid;
    }

    // -------------------------------------------------------------
    // STEP 8: SUMMARY COMPILING & PRE-SUBMISSION VIEW MODAL
    // -------------------------------------------------------------
    function compileSummaryData() {
        const tableBody = document.getElementById("summaryTableBody");
        
        const stockDate = document.getElementById("setupDate").value;
        const shopCode = document.getElementById("setupShopCode").value.trim().toUpperCase();
        
        const typeBadge = selectedType === 'PI' ? '<span class="badge-pi">PI</span>' : '<span class="badge-sst">SST</span>';
        const modesList = selectedMode.join(", ");

        let htmlBuffer = "";

        if (selectedType === 'PI') {
            // PI (Perpetual Inventory) includes the ENTIRE catalog scope.
            // Rendering a single premium consolidated row is 100% efficient and clear.
            htmlBuffer += `
                <tr class="row-highlight">
                    <td>${stockDate}</td>
                    <td>${shopCode}</td>
                    <td>${typeBadge}</td>
                    <td>${modesList}</td>
                    <td style="font-weight: 800; color: var(--color-primary);">ALL DEPARTMENTS (Complete Catalog Scope)</td>
                    <td style="font-weight: 700; color: var(--color-text-muted);">ALL GROUPS</td>
                    <td style="font-weight: 700; color: var(--color-text-muted);">ALL SUBGROUPS</td>
                </tr>
            `;
        } else if (selectedScopeDepth === 'SST') {
            // SST Depth includes entire selected departments.
            // Loop through selected departments (checkedDepts) and render a clean department-level summary.
            const checkedDepts = document.querySelectorAll(".dept-node:checked");
            checkedDepts.forEach(dNode => {
                const deptName = dNode.getAttribute("data-name");
                htmlBuffer += `
                    <tr class="row-highlight">
                        <td>${stockDate}</td>
                        <td>${shopCode}</td>
                        <td>${typeBadge}</td>
                        <td>${modesList}</td>
                        <td style="font-weight: 800; color: var(--color-text-main);">${deptName}</td>
                        <td style="font-weight: 700; color: var(--color-text-muted);">ALL GROUPS</td>
                        <td style="font-weight: 700; color: var(--color-text-muted);">ALL SUBGROUPS</td>
                    </tr>
                `;
            });
        } else {
            // Fine-grained Group/Subgroup scope
            const checkedDepts = document.querySelectorAll(".dept-node:checked");

            checkedDepts.forEach(dNode => {
                const deptId = dNode.value;
                const deptName = dNode.getAttribute("data-name");

                const checkedGroups = document.querySelectorAll(`.group-node[data-deptid="${deptId}"]:checked`);
                if (checkedGroups.length === 0) {
                    htmlBuffer += `
                        <tr class="row-highlight">
                            <td>${stockDate}</td>
                            <td>${shopCode}</td>
                            <td>${typeBadge}</td>
                            <td>${modesList}</td>
                            <td>${deptName}</td>
                            <td>All Groups</td>
                            <td>All Subgroups</td>
                        </tr>
                    `;
                } else {
                    checkedGroups.forEach(gNode => {
                        const groupId = gNode.value;
                        const groupName = gNode.getAttribute("data-name");

                        const checkedSubs = document.querySelectorAll(`.subgroup-node[data-groupid="${groupId}"]:checked`);
                        if (checkedSubs.length === 0) {
                            htmlBuffer += `
                                <tr class="row-highlight">
                                    <td>${stockDate}</td>
                                    <td>${shopCode}</td>
                                    <td>${typeBadge}</td>
                                    <td>${modesList}</td>
                                    <td>${deptName}</td>
                                    <td>${groupName}</td>
                                    <td>All Subgroups</td>
                                </tr>
                            `;
                        } else {
                            checkedSubs.forEach(sNode => {
                                const subName = sNode.getAttribute("data-name");
                                htmlBuffer += `
                                    <tr class="row-highlight">
                                        <td>${stockDate}</td>
                                        <td>${shopCode}</td>
                                        <td>${typeBadge}</td>
                                        <td>${modesList}</td>
                                        <td>${deptName}</td>
                                        <td>${groupName}</td>
                                        <td>${subName}</td>
                                    </tr>
                                `;
                            });
                        }
                    });
                }
            });
        }

        tableBody.innerHTML = htmlBuffer;
    }

    function showConfirmModal() {
        const summaryBody = document.getElementById("summaryTableBody");
        const modalSummaryBody = document.getElementById("modalSummaryTableBody");
        if (summaryBody && modalSummaryBody) {
            modalSummaryBody.innerHTML = summaryBody.innerHTML;
        }

        const toggle = document.getElementById("verificationApproveToggle");
        if (toggle) {
            toggle.checked = false;
        }
        handleVerificationApprovalChange();

        document.getElementById("confirmSubmitModal").classList.remove("hidden");
    }

    function closeConfirmModal() {
        document.getElementById("confirmSubmitModal").classList.add("hidden");
    }

    function handleVerificationApprovalChange() {
        const toggle = document.getElementById("verificationApproveToggle");
        const btnSubmit = document.getElementById("btnFinalDbSubmit");
        if (!toggle || !btnSubmit) return;

        if (toggle.checked) {
            btnSubmit.disabled = false;
            btnSubmit.className = "btn btn-primary";
        } else {
            btnSubmit.disabled = true;
            btnSubmit.className = "btn btn-disabled";
        }
    }

    function executeFinalSubmit() {
        closeConfirmModal();
        submitSetupData();
    }

    // -------------------------------------------------------------
    // FINAL SECURE AJAX DATABASE SUBMIT ACTION
    // -------------------------------------------------------------
    function submitSetupData() {
        showLoader(
            "Saving Setup Configuration...",
            "Writing parameters to the secure Oracle database and performing live catalog schema audits. Please do not refresh this page."
        );

        const stockDate = document.getElementById("setupDate").value;
        const shopCode = document.getElementById("setupShopCode").value.trim().toUpperCase();
        
        let depts = [];
        let groups = [];
        let subgroups = [];

        if (selectedType === 'PI' || selectedScopeDepth === 'SST') {
            const uniqueDepts = new Set();
            const uniqueGroups = new Set();
            const uniqueSubs = new Set();
            
            deptsData.forEach(dept => {
                uniqueDepts.add(dept.name);
                dept.groups.forEach(group => {
                    uniqueGroups.add(group.name);
                    group.subgroups.forEach(sub => {
                        uniqueSubs.add(sub.name);
                    });
                });
            });
            
            depts = Array.from(uniqueDepts);
            groups = Array.from(uniqueGroups);
            subgroups = Array.from(uniqueSubs);
        } else {
            document.querySelectorAll(".dept-node:checked").forEach(n => depts.push(n.getAttribute("data-name")));
            document.querySelectorAll(".group-node:checked").forEach(n => groups.push(n.getAttribute("data-name")));
            document.querySelectorAll(".subgroup-node:checked").forEach(n => subgroups.push(n.getAttribute("data-name")));
        }

        const formData = new FormData();
        formData.append("shop_code", shopCode);
        formData.append("stock_date", stockDate);
        formData.append("audit_type", selectedType);
        formData.append("audit_mode", selectedMode.join(", "));
        formData.append("depts", depts.join(", "));
        formData.append("groups", groups.join(", "));
        formData.append("subgroups", subgroups.join(", "));

        // AJAX Post fetch to our MVC route secure endpoint!
        fetch("index.php?route=audit/save", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            hideLoader();
            if (data.trim() === "ok") {
                isSetupSubmitted = true;
                localStorage.setItem("melcom_stock_audit_submitted", "true");
                localStorage.setItem("melcom_stock_audit_data", JSON.stringify({
                    shopCode: shopCode,
                    stockDate: stockDate,
                    auditType: selectedType,
                    auditMode: selectedMode.join(", "),
                    depts: depts.join(", "),
                    groups: groups.join(", "),
                    subgroups: subgroups.join(", ")
                }));
                updateWizardState();
                printSetupSheet(true); // Automatically triggers windows print layout upon final confirmed submit!
            } else {
                alert("Submission Failed: " + data);
            }
        })
        .catch(err => {
            hideLoader();
            alert("Network Error: Could not connect to the database save script.");
        });
    }

    // -------------------------------------------------------------
    // DYNAMIC EXPORT TO CSV ENGINE
    // -------------------------------------------------------------
    function exportSetupToCsv() {
        const tableRows = [];
        const trs = document.querySelectorAll("#summaryTableBody tr");
        
        const stockDateDefault = document.getElementById("setupDate").value;
        const shopCodeDefault = document.getElementById("setupShopCode").value.trim().toUpperCase();
        
        trs.forEach(tr => {
            const tds = tr.querySelectorAll("td");
            if (tds.length === 7) {
                const stock_date = (tds[0].innerText.trim() === "-" || tds[0].innerText.trim() === "") ? stockDateDefault : tds[0].innerText.trim();
                const shop_code = (tds[1].innerText.trim() === "-" || tds[1].innerText.trim() === "") ? shopCodeDefault : tds[1].innerText.trim();
                const audit_type = (tds[2].innerText.trim() === "-" || tds[2].innerText.trim() === "") ? selectedType : tds[2].innerText.trim();
                const scanning_mode = (tds[3].innerText.trim() === "-" || tds[3].innerText.trim() === "") ? selectedMode.join(", ") : tds[3].innerText.trim();
                const department = tds[4].innerText.trim();
                const group = tds[5].innerText.trim();
                const subgroup = tds[6].innerText.trim();
                
                tableRows.push({ stock_date, shop_code, audit_type, scanning_mode, department, group, subgroup });
            }
        });

        if (tableRows.length === 0) {
            alert("No data available to export.");
            return;
        }

        // POST dynamic export submission form to export.php
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "export.php";
        form.style.display = "none";

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "export_data";
        input.value = JSON.stringify(tableRows);
        form.appendChild(input);

        const stockDate = document.getElementById("setupDate").value;
        const shopCode = document.getElementById("setupShopCode").value.trim().toUpperCase();
        
        const inputDate = document.createElement("input");
        inputDate.type = "hidden";
        inputDate.name = "stock_date";
        inputDate.value = stockDate;
        form.appendChild(inputDate);

        const inputShop = document.createElement("input");
        inputShop.type = "hidden";
        inputShop.name = "shop_code";
        inputShop.value = shopCode;
        form.appendChild(inputShop);

        const inputType = document.createElement("input");
        inputType.type = "hidden";
        inputType.name = "audit_type";
        inputType.value = selectedType;
        form.appendChild(inputType);

        const inputMode = document.createElement("input");
        inputMode.type = "hidden";
        inputMode.name = "scanning_mode";
        inputMode.value = selectedMode.join(", ");
        form.appendChild(inputMode);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    // -------------------------------------------------------------
    // HIGH-FIDELITY PRINT REPORT COMPILER
    // -------------------------------------------------------------
    function printSetupSheet(triggerPrint = true) {
        const tableRows = [];
        const trs = document.querySelectorAll("#summaryTableBody tr");
        
        const stockDateDefault = document.getElementById("setupDate").value;
        const shopCodeDefault = document.getElementById("setupShopCode").value.trim().toUpperCase();
        
        trs.forEach(tr => {
            const tds = tr.querySelectorAll("td");
            if (tds.length === 7) {
                const stock_date = (tds[0].innerText.trim() === "-" || tds[0].innerText.trim() === "") ? stockDateDefault : tds[0].innerText.trim();
                const shop_code = (tds[1].innerText.trim() === "-" || tds[1].innerText.trim() === "") ? shopCodeDefault : tds[1].innerText.trim();
                const audit_type = (tds[2].innerText.trim() === "-" || tds[2].innerText.trim() === "") ? selectedType : tds[2].innerText.trim();
                const scanning_mode = (tds[3].innerText.trim() === "-" || tds[3].innerText.trim() === "") ? selectedMode.join(", ") : tds[3].innerText.trim();
                const department = tds[4].innerText.trim();
                const group = tds[5].innerText.trim();
                const subgroup = tds[6].innerText.trim();
                
                tableRows.push({ stock_date, shop_code, audit_type, scanning_mode, department, group, subgroup });
            }
        });

        if (tableRows.length === 0) {
            alert("No data available to print.");
            return;
        }

        let rowsHtml = "";
        tableRows.forEach(row => {
            const typeText = row.audit_type.replace(/<[^>]*>/g, '');
            rowsHtml += `
                <tr>
                    <td>${row.stock_date}</td>
                    <td>${row.shop_code}</td>
                    <td>${typeText}</td>
                    <td>${row.scanning_mode}</td>
                    <td>${row.department}</td>
                    <td>${row.group}</td>
                    <td>${row.subgroup}</td>
                </tr>
            `;
        });

        const loginEmail = "<?php echo htmlspecialchars($email); ?>";
        const shopCode = shopCodeDefault;

        let printScript = "";
        if (triggerPrint) {
            printScript = `
                <script>
                    window.addEventListener('load', () => {
                        setTimeout(() => {
                            window.print();
                        }, 600);
                    });
                <\/script>
            `;
        }

        const printWindow = window.open("", "_blank");
        if (!printWindow) {
            alert("Popup blocker prevented opening the print report window. Please allow popups for this site.");
            return;
        }

        printWindow.document.open();
        printWindow.document.write(`
<!DOCTYPE html>
<html>
<head>
    <title>Print Audit Setup - ${shopCode}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
        body {
            font-family: 'Outfit', sans-serif;
            color: #1e293b;
            padding: 2rem;
            margin: 0;
            background-color: #ffffff;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #006D44;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .logo-circle {
            width: 36px;
            height: 36px;
            background-color: #006D44;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 16px;
        }
        .brand-text-wrapper {
            display: flex;
            flex-direction: column;
        }
        .brand-title {
            font-size: 20px;
            font-weight: 900;
            color: #006D44;
            margin: 0;
            line-height: 1.1;
        }
        .brand-subtitle {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
            font-weight: 700;
        }
        .meta-info {
            text-align: right;
            font-size: 12px;
            color: #64748b;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .meta-info strong {
            color: #0f172a;
        }
        .report-title {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th {
            background-color: #006D44;
            color: #ffffff;
            font-weight: 800;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            border: 1px solid #005a38;
            text-align: left;
        }
        td {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            color: #334155;
            font-weight: 500;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            margin-top: 4rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 1rem;
            font-size: 10px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
            font-weight: 600;
        }
        @media print {
            body {
                padding: 0;
            }
            @page {
                size: auto;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">
            <div class="logo-circle">M</div>
            <div class="brand-text-wrapper">
                <h1 class="brand-title">MELCOM</h1>
                <p class="brand-subtitle">Audit Setup System</p>
            </div>
        </div>
        <div class="meta-info">
            <span>Auditor: <strong>${loginEmail}</strong></span>
            <span>Audit Date: <strong>${stockDateDefault}</strong></span>
        </div>
    </div>
    <div class="report-title">Setup Configuration Report</div>
    <table>
        <thead>
            <tr>
                <th>Stock Date</th>
                <th>Shop Code</th>
                <th>Audit Type</th>
                <th>Mode</th>
                <th>Department</th>
                <th>Group</th>
                <th>Subgroup</th>
            </tr>
        </thead>
        <tbody>
            ${rowsHtml}
        </tbody>
    </table>
    <div class="footer">
        <span>Melcom Shop Setup Initialization System</span>
        <span>Report Confidential - Internal Audit Use Only</span>
    </div>
    ${printScript}
</body>
</html>
        `);
        printWindow.document.close();
    }
</script>
