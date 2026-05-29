<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - MAIN DASHBOARD FRAMEWORK
// The unified tabbed corporate dashboard holding Stock Audit & Checklist views.
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

<div class="main-card">
    
    <!-- LEFT SIDEBAR: CORPORATE BRAND & PROGRESS TRACKERS -->
    <aside class="sidebar">
        <div class="sidebar-top-section">
            <!-- Branding Header -->
            <div class="sidebar-header">
                <img src="IMG/logo_circle.png" alt="Melcom Logo" class="sidebar-logo">
                <div class="sidebar-brand">
                    <h1>Melcom Audit</h1>
                    <span>Enterprise Portal</span>
                </div>
            </div>

            <!-- Tab Switcher Navigation -->
            <div style="display: flex; flex-direction: column; gap: 0.35rem; margin-top: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; width: 100%;">
                <div id="menuTabStockAudit" class="step-node" onclick="switchTab('stock_audit')" style="cursor: pointer; padding: 0.5rem 0.75rem; border-radius: 12px; transition: var(--transition-fast);">
                    <div id="circleTabAudit" class="step-circle active" style="font-size: 14px; width: 28px; height: 28px; font-weight: 800;">📦</div>
                    <span id="textTabAudit" class="step-text active" style="font-size: 13px; font-weight: 900;">Stock Audit</span>
                </div>
                <div id="menuTabChecklist" class="step-node" onclick="switchTab('checklist')" style="cursor: pointer; padding: 0.5rem 0.75rem; border-radius: 12px; transition: var(--transition-fast);">
                    <div id="circleTabCheck" class="step-circle upcoming" style="font-size: 14px; width: 28px; height: 28px; font-weight: 800;">📋</div>
                    <span id="textTabCheck" class="step-text upcoming" style="font-size: 13px; font-weight: 700;">Checklist</span>
                </div>
            </div>

            <!-- DYNAMIC SIDEBAR 1: STOCK AUDIT PROGRESS TRACKER -->
            <div id="sidebarProgressTracker" class="sidebar-progress-container" style="width: 100%;">
                <div class="sidebar-progress-labels" style="margin-top: 1rem;">
                    <span>Progress</span>
                    <span id="sidebarProgressText" class="progress-number">3/8 Completed</span>
                </div>
                <div class="sidebar-progress-track">
                    <div id="sidebarProgress" class="sidebar-progress-fill" style="width: 37.5%;"></div>
                </div>

                <nav class="sidebar-nav" style="margin-top: 1.5rem;">
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

            <!-- DYNAMIC SIDEBAR 2: CHECKLIST TRACKER (Hidden by default) -->
            <div id="sidebarChecklistTracker" class="sidebar-progress-container hidden" style="width: 100%;">
                <div class="sidebar-progress-labels" style="margin-top: 1rem;">
                    <span>Scope</span>
                    <span class="progress-number" style="color: var(--color-primary);">Checklist Config</span>
                </div>
                <div class="sidebar-progress-track">
                    <div class="sidebar-progress-fill" style="width: 100%;"></div>
                </div>

                <nav class="sidebar-nav" style="margin-top: 1.5rem;">
                    <div class="step-node">
                        <div class="step-circle active">1</div>
                        <span class="step-text active">Store Environment</span>
                    </div>
                    <div class="step-node">
                        <div class="step-circle upcoming">2</div>
                        <span class="step-text upcoming">Staff Checklist</span>
                    </div>
                    <div class="step-node">
                        <div class="step-circle upcoming">3</div>
                        <span class="step-text upcoming">Variance Audit</span>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Database Connection Status Widget (Bottom section of the sidebar) -->
        <div class="db-status-widget">
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
    </aside>

    <!-- RIGHT COLUMN: DYNAMIC WORKSPACE PANEL -->
    <main class="workspace">
        
        <!-- Corporate Header inside workspace with Auditor account showing -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem; margin-bottom: 1.5rem; user-select: none;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; color: var(--color-primary); stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span style="font-size: 13px; font-weight: 800; text-transform: uppercase; color: var(--color-text-main); letter-spacing: 0.025em;">Corporate Secure Workspace</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="display: flex; flex-direction: column; text-align: right;">
                    <span style="font-size: 9px; font-weight: 900; text-transform: uppercase; color: var(--color-text-light); letter-spacing: 0.05em;">Auditor Profile</span>
                    <span style="font-size: 12px; font-weight: 700; color: var(--color-text-main);"><?php echo htmlspecialchars($email); ?></span>
                </div>
                
                <!-- Exit / Logout button -->
                <button type="button" onclick="logoutSession()" class="btn-exit" style="position: static; color: var(--color-text-light);" title="Logout Auditor Session">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </div>
        </div>

        <!-- DYNAMIC TABBED VIEWS -->
        <div id="tabContentStockAudit" style="display: flex; flex-direction: column; flex: 1; min-height: 0; width: 100%;">
            <?php require_once __DIR__ . '/stock_audit.php'; ?>
        </div>

        <div id="tabContentChecklist" class="hidden" style="display: flex; flex-direction: column; flex: 1; min-height: 0; width: 100%;">
            <?php require_once __DIR__ . '/checklist.php'; ?>
        </div>

    </main>
</div>

<script>
    /**
     * Handles instant responsive tab-switching inside the portal.
     */
    function switchTab(tabName) {
        const tabStockAudit = document.getElementById("tabContentStockAudit");
        const tabChecklist = document.getElementById("tabContentChecklist");
        
        const menuStockAudit = document.getElementById("menuTabStockAudit");
        const menuChecklist = document.getElementById("menuTabChecklist");

        const circleTabAudit = document.getElementById("circleTabAudit");
        const circleTabCheck = document.getElementById("circleTabCheck");
        const textTabAudit = document.getElementById("textTabAudit");
        const textTabCheck = document.getElementById("textTabCheck");

        const sidebarStockAudit = document.getElementById("sidebarProgressTracker");
        const sidebarChecklist = document.getElementById("sidebarChecklistTracker");

        if (tabName === 'stock_audit') {
            tabStockAudit.classList.remove("hidden");
            tabChecklist.classList.add("hidden");
            
            circleTabAudit.className = "step-circle active";
            circleTabCheck.className = "step-circle upcoming";
            textTabAudit.className = "step-text active";
            textTabAudit.style.fontWeight = "900";
            textTabCheck.className = "step-text upcoming";
            textTabCheck.style.fontWeight = "700";

            sidebarStockAudit.classList.remove("hidden");
            sidebarChecklist.classList.add("hidden");
        } else {
            tabStockAudit.classList.add("hidden");
            tabChecklist.classList.remove("hidden");
            
            circleTabAudit.className = "step-circle upcoming";
            circleTabCheck.className = "step-circle active";
            textTabAudit.className = "step-text upcoming";
            textTabAudit.style.fontWeight = "700";
            textTabCheck.className = "step-text active";
            textTabCheck.style.fontWeight = "900";

            sidebarStockAudit.classList.add("hidden");
            sidebarChecklist.classList.remove("hidden");
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
