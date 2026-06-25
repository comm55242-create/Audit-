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

if ($db_status !== 'ONLINE') {
    throw new Exception("Melcom Audit System Database is Offline: Could not establish a secure connection to the Oracle database. Ensure the listener is active and network configurations are correct.\nDiagnostic error: " . $db_error);
}

if (empty($stats['tree'])) {
    throw new Exception("Melcom Audit System Configuration Error: Oracle view VW_STK_DEPT@DB_LINK_SHOP returned 0 departments. Ensure the remote database link is correctly synchronized and active.");
}

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

        <!-- Scrollable Sidebar Content: Tabs & Progress Trackers -->
        <div class="dashboard-sidebar-scroll" style="padding-top: 1rem;">

            <!-- ACCORDION 1: STOCK AUDIT -->
            <button id="menuTabStockAudit" class="dashboard-tab-item active" onclick="switchTab('stock_audit')" style="width: 100%; justify-content: flex-start; margin-bottom: 0.5rem; gap: 0.5rem;">
                <span class="dashboard-tab-icon" style="display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
                <span>Stock Audit</span>
            </button>
            <div id="sidebarProgressTracker" style="width: 100%; padding-left: 0.5rem; margin-bottom: 1rem;">
                <div class="sidebar-progress-labels" style="margin-top: 0.5rem;">
                    <span>Progress</span>
                    <span id="sidebarProgressText" class="progress-number">3/9 Completed</span>
                </div>
                <div class="sidebar-progress-track">
                    <div id="sidebarProgress" class="sidebar-progress-fill" style="width: 37.5%;"></div>
                </div>

                <nav class="sidebar-nav" style="margin-top: 1.25rem;">
                    <!-- Step 1 & 2 Completed states -->
                    <div class="step-node">
                        <div class="step-circle completed"><svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
                        <span class="step-text completed">Sign In</span>
                    </div>
                    <div class="step-node">
                        <div class="step-circle completed"><svg style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
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
                    <!-- Step 7: Groups -->
                    <div id="menuStep-7" class="step-node">
                        <div id="circle-7" class="step-circle upcoming">7</div>
                        <span id="text-7" class="step-text upcoming">Groups</span>
                    </div>
                    <!-- Step 8: Subgroups -->
                    <div id="menuStep-8" class="step-node">
                        <div id="circle-8" class="step-circle upcoming">8</div>
                        <span id="text-8" class="step-text upcoming">Subgroups</span>
                    </div>
                    <!-- Step 9: Summary & Export -->
                    <div id="menuStep-9" class="step-node">
                        <div id="circle-9" class="step-circle upcoming">9</div>
                        <span id="text-9" class="step-text upcoming">Summary & Export</span>
                    </div>
                </nav>
            </div>

            <!-- ACCORDION 2: DETAILS TRACKER -->
            <button id="menuTabDetails" class="dashboard-tab-item" onclick="switchTab('details')" style="width: 100%; justify-content: flex-start; margin-bottom: 0.5rem; gap: 0.5rem;">
                <span class="dashboard-tab-icon" style="display: flex; align-items: center; justify-content: center;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; stroke-width: 2.25;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
                <span>Check_list</span>
            </button>
            <div id="sidebarDetailsTracker" class="hidden" style="width: 100%; padding-left: 0.5rem; margin-bottom: 1rem;">
                <div class="sidebar-progress-labels" style="margin-top: 0.5rem;">
                    <span>Progress</span>
                    <span id="sidebarDetailsProgressText" class="progress-number">0/4 Completed</span>
                </div>
                <div class="sidebar-progress-track">
                    <div id="sidebarDetailsProgress" class="sidebar-progress-fill" style="width: 0%;"></div>
                </div>

                <nav class="sidebar-nav" style="margin-top: 1.25rem;">
                    <div class="step-node" id="menuDetailsStep-1">
                        <div id="circleDetails-1" class="step-circle active">1</div>
                        <span id="textDetails-1" class="step-text active">Stock Take Info</span>
                    </div>
                    <div class="step-node" id="menuDetailsStep-2">
                        <div id="circleDetails-2" class="step-circle upcoming">2</div>
                        <span id="textDetails-2" class="step-text upcoming">Pre-Stock Checklist</span>
                    </div>
                    <div class="step-node" id="menuDetailsStep-3">
                        <div id="circleDetails-3" class="step-circle upcoming">3</div>
                        <span id="textDetails-3" class="step-text upcoming">Print</span>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Global Refresh Button -->
        <div style="padding: 1rem; padding-top: 0;">
            <button type="button" onclick="openResetModal()" class="btn btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: #fff1f2; color: #e11d48; border-color: #fda4af; transition: all 0.2s ease;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; stroke-width: 2.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" /><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5" /></svg>
                <span style="font-weight: 700; font-size: 13px;">Reset App</span>
            </button>
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
        </div>
    </main>
    <!-- Reset Confirmation Modal -->
    <div id="resetConfirmModal" class="modal-overlay hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; padding: 2rem; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; color: #e11d48;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; stroke-width: 2;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 style="margin: 0; font-size: 1.25rem;">Confirm Reset</h3>
            </div>
            <p style="margin-bottom: 2rem; color: #475569; font-size: 0.95rem; line-height: 1.5;">Are you sure you want to reset the application? This will clear all unsaved audit setups and checklist data.</p>
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button onclick="closeResetModal()" class="btn btn-secondary" style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; font-weight: 600;">Cancel</button>
                <button onclick="executeHardReset()" class="btn btn-primary" style="padding: 0.5rem 1rem; border-radius: 6px; border: none; background: #e11d48; color: #fff; cursor: pointer; font-weight: 600;">Yes, Reset App</button>
            </div>
        </div>
    </div>
    <!-- Logout Confirmation Modal -->
    <div id="logoutConfirmModal" class="modal-overlay hidden" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.7); z-index: 9999; display: flex; align-items: center; justify-content: center;">
        <div class="modal-content" style="background: white; padding: 2rem; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; color: #4f46e5;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; stroke-width: 2;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <h3 style="margin: 0; font-size: 1.25rem;">Confirm Logout</h3>
            </div>
            <p style="margin-bottom: 2rem; color: #475569; font-size: 0.95rem; line-height: 1.5;">Are you sure you want to exit and close the secure Auditing session?</p>
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button onclick="closeLogoutModal()" class="btn btn-secondary" style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #475569; cursor: pointer; font-weight: 600;">Cancel</button>
                <button onclick="executeLogout()" class="btn btn-primary" style="padding: 0.5rem 1rem; border-radius: 6px; border: none; background: #4f46e5; color: #fff; cursor: pointer; font-weight: 600;">Yes, Logout</button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Handles instant responsive tab-switching inside the portal.
     */
    function switchTab(tabName) {
        const tabStockAudit = document.getElementById("tabContentStockAudit");
        const tabDetails = document.getElementById("tabContentDetails");
        
        const menuStockAudit = document.getElementById("menuTabStockAudit");
        const menuDetails = document.getElementById("menuTabDetails");

        const sidebarStockAudit = document.getElementById("sidebarProgressTracker");
        const sidebarDetails = document.getElementById("sidebarDetailsTracker");

        if (tabName === 'stock_audit') {
            if (menuStockAudit.classList.contains("active")) {
                sidebarStockAudit.classList.toggle("hidden");
            } else {
                tabStockAudit.classList.remove("hidden");
                tabDetails.classList.add("hidden");
                
                menuStockAudit.classList.add("active");
                menuDetails.classList.remove("active");

                sidebarStockAudit.classList.remove("hidden");
                sidebarDetails.classList.add("hidden");
            }
        } else {
            if (menuDetails.classList.contains("active")) {
                sidebarDetails.classList.toggle("hidden");
            } else {
                tabStockAudit.classList.add("hidden");
                tabDetails.classList.remove("hidden");
                
                menuStockAudit.classList.remove("active");
                menuDetails.classList.add("active");

                sidebarStockAudit.classList.add("hidden");
                sidebarDetails.classList.remove("hidden");

                // Safe state triggers
                if (window.loadDetailsState) window.loadDetailsState();
            }
        }
    }

    /**
     * Discards Auditor Session and logs out securely.
     */
    function logoutSession() {
        document.getElementById("logoutConfirmModal").classList.remove("hidden");
    }

    function closeLogoutModal() {
        document.getElementById("logoutConfirmModal").classList.add("hidden");
    }

    function executeLogout() {
        closeLogoutModal();
        const loader = document.getElementById('global-page-loader');
        if (loader) {
            loader.style.display = 'flex';
            loader.style.visibility = 'visible';
            loader.style.opacity = '1';
        }
        location.href = "index.php?route=logout";
    }

    /**
     * Modal Reset Logic
     */
    function openResetModal() {
        document.getElementById("resetConfirmModal").classList.remove("hidden");
    }

    function closeResetModal() {
        document.getElementById("resetConfirmModal").classList.add("hidden");
    }

    function executeHardReset() {
        closeResetModal();
        const loader = document.getElementById('global-page-loader');
        if (loader) {
            loader.style.display = 'flex';
            loader.style.visibility = 'visible';
            loader.style.opacity = '1';
        }
        localStorage.clear();
        setTimeout(() => {
            window.location.reload(true);
        }, 100);
    }
</script>

<!-- Offline QR Code Library -->
<script src="js/qrcode.min.js"></script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
