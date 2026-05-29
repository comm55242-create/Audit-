<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - MAIN DASHBOARD FRAMEWORK
// Modern sidebar + content panel corporate dashboard with Stock Audit & Checklist tabs.
// ==========================================================================

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/AuditModel.php';

$stats = AuditModel::getDatabaseStats();
$db_status = $stats['status'];
$db_host = $stats['host'];
$db_depts = $stats['depts'];
$db_groups = $stats['groups'];
$db_subgroups = $stats['subgroups'];
$db_error = $stats['error'];

$email = isset($_SESSION['logged_in_email']) ? $_SESSION['logged_in_email'] : 'auditor@melcomdc.com';
?>

<div class="dashboard-shell">

    <!-- SIDEBAR -->
    <aside class="dashboard-sidebar">
        <!-- Brand Header -->
        <div class="dashboard-sidebar-brand">
            <img src="IMG/logo_circle.png" alt="Melcom Logo">
            <div class="dashboard-sidebar-brand-text">
                <h1>Melcom Audit</h1>
                <span>Enterprise Portal</span>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="dashboard-tab-menu">
            <button id="menuTabStockAudit" class="dashboard-tab-item active" onclick="switchTab('stock_audit')">
                <span class="dashboard-tab-icon" style="display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
                <span>Stock Audit</span>
            </button>
            <button id="menuTabDetails" class="dashboard-tab-item" onclick="switchTab('details')">
                <span class="dashboard-tab-icon" style="display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
                <span>Details</span>
            </button>
            <button id="menuTabChecklist" class="dashboard-tab-item" onclick="switchTab('checklist')">
                <span class="dashboard-tab-icon" style="display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
                <span>Checklist</span>
            </button>
        </div>

        <!-- Scrollable Sidebar Content: Progress Trackers -->
        <div class="dashboard-sidebar-scroll">

            <!-- DYNAMIC SIDEBAR 1: STOCK AUDIT PROGRESS TRACKER -->
            <div id="sidebarProgressTracker" style="width: 100%;">
                <div class="sidebar-progress-labels" style="margin-top: 0.5rem;">
                    <span>Progress</span>
                    <span id="sidebarProgressText" class="progress-number">3/8 Completed</span>
                </div>
                <div class="sidebar-progress-track">
                    <div id="sidebarProgress" class="sidebar-progress-fill" style="width: 37.5%;"></div>
                </div>

                <nav class="sidebar-nav" style="margin-top: 1.25rem;">
                    <!-- Step 1 & 2 Completed states -->
                    <div class="step-node">
                        <div class="step-circle completed">✓</div>
                        <span class="step-text completed">Sign In</span>
                    </div>
                    <div class="step-node">
                        <div class="step-circle completed">✓</div>
                        <span class="step-text completed">OTP Verification</span>
                    </div>
                    <!-- Step 3: Shop Setup -->
                    <div id="menuStep-3" class="step-node">
                        <div id="circle-3" class="step-circle active">3</div>
                        <span id="text-3" class="step-text active">Shop Setup</span>
                    </div>
                    <!-- Step 4: Audit Type -->
                    <div id="menuStep-4" class="step-node">
                        <div id="circle-4" class="step-circle upcoming">4</div>
                        <span id="text-4" class="step-text upcoming">Audit Type</span>
                    </div>
                    <!-- Step 5: Mode -->
                    <div id="menuStep-5" class="step-node">
                        <div id="circle-5" class="step-circle upcoming">5</div>
                        <span id="text-5" class="step-text upcoming">Mode</span>
                    </div>
                    <!-- Step 6: Department Selector -->
                    <div id="menuStep-6" class="step-node">
                        <div id="circle-6" class="step-circle upcoming">6</div>
                        <span id="text-6" class="step-text upcoming">Department Selector</span>
                    </div>
                    <!-- Step 7: Groups & Subgroups -->
                    <div id="menuStep-7" class="step-node">
                        <div id="circle-7" class="step-circle upcoming">7</div>
                        <span id="text-7" class="step-text upcoming">Groups & Subgroups</span>
                    </div>
                    <!-- Step 8: Summary & Export -->
                    <div id="menuStep-8" class="step-node">
                        <div id="circle-8" class="step-circle upcoming">8</div>
                        <span id="text-8" class="step-text upcoming">Summary & Export</span>
                    </div>
                </nav>
            </div>

            <!-- DYNAMIC SIDEBAR 2: DETAILS TRACKER (Hidden by default) -->
            <div id="sidebarDetailsTracker" class="hidden" style="width: 100%;">
                <div class="sidebar-progress-labels" style="margin-top: 0.5rem;">
                    <span>Progress</span>
                    <span id="sidebarDetailsProgressText" class="progress-number">0/4 Completed</span>
                </div>
                <div class="sidebar-progress-track">
                    <div id="sidebarDetailsProgress" class="sidebar-progress-fill" style="width: 0%;"></div>
                </div>

                <nav class="sidebar-nav" style="margin-top: 1.25rem;">
                    <div class="step-node">
                        <div class="step-circle active">1</div>
                        <span class="step-text active">Stock Take Info</span>
                    </div>
                    <div class="step-node">
                        <div class="step-circle active">2</div>
                        <span class="step-text active">Staff Attendance</span>
                    </div>
                    <div class="step-node">
                        <div class="step-circle active">3</div>
                        <span class="step-text active">Zone Tracker</span>
                    </div>
                    <div class="step-node">
                        <div class="step-circle active">4</div>
                        <span class="step-text active">Scan Control</span>
                    </div>
                </nav>
            </div>

            <!-- DYNAMIC SIDEBAR 3: CHECKLIST TRACKER (Hidden by default) -->
            <div id="sidebarChecklistTracker" class="hidden" style="width: 100%;">
                <div class="sidebar-progress-labels" style="margin-top: 0.5rem;">
                    <span>Progress</span>
                    <span id="sidebarChecklistProgressText" class="progress-number">0/10 Completed</span>
                </div>
                <div class="sidebar-progress-track">
                    <div id="sidebarChecklistProgress" class="sidebar-progress-fill" style="width: 0%;"></div>
                </div>

                <nav class="sidebar-nav" style="margin-top: 1.25rem;">
                    <div class="step-node">
                        <div class="step-circle active">1</div>
                        <span class="step-text active">Pre-Stock Workflow</span>
                    </div>
                    <div class="step-node">
                        <div class="step-circle active">2</div>
                        <span class="step-text active">Report Alert Checklist</span>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Database Connection Status Widget -->
        <div class="dashboard-sidebar-footer">
            <div class="db-status-widget" style="margin: 0; padding: 0; border: none; background: none;">
                <div class="db-status-header">
                    <span class="db-status-title">Oracle Database</span>
                    <?php if ($db_status === 'ONLINE'): ?>
                        <span class="db-status-badge" style="color: var(--color-success);">
                            <span class="db-status-dot online"></span>
                            Online
                        </span>
                    <?php else: ?>
                        <span class="db-status-badge" style="color: var(--color-danger);">
                            <span class="db-status-dot offline"></span>
                            Offline
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="db-status-details">
                    <div>
                        <span class="label">Host:</span>
                        <span class="value"><?php echo htmlspecialchars($db_host); ?></span>
                    </div>
                    <div>
                        <span class="label">Depts:</span>
                        <span class="value"><?php echo count($db_depts); ?></span>
                    </div>
                    <div>
                        <span class="label">Groups:</span>
                        <span class="value"><?php echo count($db_groups); ?></span>
                    </div>
                    <div>
                        <span class="label">Subgroups:</span>
                        <span class="value"><?php 
                            $subCount = 0;
                            foreach ($db_subgroups as $grp => $subs) {
                                $subCount += count($subs);
                            }
                            echo $subCount;
                        ?></span>
                    </div>
                </div>

                <?php if (!empty($db_error)): ?>
                    <div class="db-status-error" title="<?php echo htmlspecialchars($db_error); ?>">
                        <strong>Diagnostic:</strong> <?php echo htmlspecialchars($db_error); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT PANEL -->
    <main class="dashboard-main">
        <!-- Top Bar -->
        <div class="dashboard-topbar">
            <div class="dashboard-topbar-left">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Corporate Secure Workspace</span>
            </div>
            
            <div class="dashboard-topbar-right">
                <div class="dashboard-topbar-user">
                    <span class="label">Auditor Profile</span>
                    <span class="value"><?php echo htmlspecialchars($email); ?></span>
                </div>
                
                <button type="button" onclick="logoutSession()" class="dashboard-topbar-logout" title="Logout Auditor Session">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </div>
        </div>

        <!-- Dynamic Tabbed Content -->
        <div class="dashboard-content">
            <div id="tabContentStockAudit" style="display: flex; flex-direction: column; flex: 1; min-height: 0; width: 100%;">
                <?php require_once __DIR__ . '/stock_audit.php'; ?>
            </div>

            <div id="tabContentDetails" class="hidden" style="display: flex; flex-direction: column; flex: 1; min-height: 0; width: 100%;">
                <?php require_once __DIR__ . '/details.php'; ?>
            </div>

            <div id="tabContentChecklist" class="hidden" style="display: flex; flex-direction: column; flex: 1; min-height: 0; width: 100%;">
                <?php require_once __DIR__ . '/checklist.php'; ?>
            </div>
        </div>
    </main>
</div>

<script>
    /**
     * Handles instant responsive tab-switching inside the portal.
     */
    function switchTab(tabName) {
        const tabStockAudit = document.getElementById("tabContentStockAudit");
        const tabDetails = document.getElementById("tabContentDetails");
        const tabChecklist = document.getElementById("tabContentChecklist");
        
        const menuStockAudit = document.getElementById("menuTabStockAudit");
        const menuDetails = document.getElementById("menuTabDetails");
        const menuChecklist = document.getElementById("menuTabChecklist");

        const sidebarStockAudit = document.getElementById("sidebarProgressTracker");
        const sidebarDetails = document.getElementById("sidebarDetailsTracker");
        const sidebarChecklist = document.getElementById("sidebarChecklistTracker");

        if (tabName === 'stock_audit') {
            tabStockAudit.classList.remove("hidden");
            tabDetails.classList.add("hidden");
            tabChecklist.classList.add("hidden");
            
            menuStockAudit.classList.add("active");
            menuDetails.classList.remove("active");
            menuChecklist.classList.remove("active");

            sidebarStockAudit.classList.remove("hidden");
            sidebarDetails.classList.add("hidden");
            sidebarChecklist.classList.add("hidden");
        } else if (tabName === 'details') {
            tabStockAudit.classList.add("hidden");
            tabDetails.classList.remove("hidden");
            tabChecklist.classList.add("hidden");
            
            menuStockAudit.classList.remove("active");
            menuDetails.classList.add("active");
            menuChecklist.classList.remove("active");

            sidebarStockAudit.classList.add("hidden");
            sidebarDetails.classList.remove("hidden");
            sidebarChecklist.classList.add("hidden");

            // Safe state triggers
            if (window.loadDetailsState) window.loadDetailsState();
        } else {
            tabStockAudit.classList.add("hidden");
            tabDetails.classList.add("hidden");
            tabChecklist.classList.remove("hidden");
            
            menuStockAudit.classList.remove("active");
            menuDetails.classList.remove("active");
            menuChecklist.classList.add("active");

            sidebarStockAudit.classList.add("hidden");
            sidebarDetails.classList.add("hidden");
            sidebarChecklist.classList.remove("hidden");

            // Safe state triggers
            if (window.loadChecklistState) window.loadChecklistState();
        }
    }

    /**
     * Discards Auditor Session and logs out securely.
     */
    function logoutSession() {
        if (confirm("Are you sure you want to exit and close the secure Auditing session?")) {
            location.href = "index.php?route=logout";
        }
    }
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
