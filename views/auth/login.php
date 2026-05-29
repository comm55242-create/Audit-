<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - LOGIN VIEW
// Dedicated Sign In page containing the premium corporate card UI (Step 1).
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

$login_error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']);
?>

<div class="main-card">
    <!-- LEFT SIDEBAR: PROGRESS TRACKER -->
    <aside class="sidebar">
        <div class="sidebar-top-section">
            <!-- Branding Header -->
            <div class="sidebar-header">
                <img src="IMG/logo_circle.png" alt="Melcom Logo" class="sidebar-logo">
                <div class="sidebar-brand">
                    <h1>Melcom Audit</h1>
                    <span>Setup Wizard</span>
                </div>
            </div>

            <!-- Progress percentage bar -->
            <div class="sidebar-progress-container">
                <div class="sidebar-progress-labels">
                    <span>Progress</span>
                    <span class="progress-number">0/8 Completed</span>
                </div>
                <div class="sidebar-progress-track">
                    <div class="sidebar-progress-fill" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Checklist step nodes -->
            <nav class="sidebar-nav">
                <div class="step-node">
                    <div class="step-circle active">1</div>
                    <span class="step-text active">Sign In</span>
                </div>
                <div class="step-node">
                    <div class="step-circle upcoming">2</div>
                    <span class="step-text upcoming">OTP Verification</span>
                </div>
                <div class="step-node">
                    <div class="step-circle upcoming">3</div>
                    <span class="step-text upcoming">Shop Setup</span>
                </div>
                <div class="step-node">
                    <div class="step-circle upcoming">4</div>
                    <span class="step-text upcoming">Audit Type</span>
                </div>
                <div class="step-node">
                    <div class="step-circle upcoming">5</div>
                    <span class="step-text upcoming">Mode</span>
                </div>
                <div class="step-node">
                    <div class="step-circle upcoming">6</div>
                    <span class="step-text upcoming">Department Selector</span>
                </div>
                <div class="step-node">
                    <div class="step-circle upcoming">7</div>
                    <span class="step-text upcoming">Groups & Subgroups</span>
                </div>
                <div class="step-node">
                    <div class="step-circle upcoming">8</div>
                    <span class="step-text upcoming">Summary & Export</span>
                </div>
            </nav>
        </div>

        <!-- Database Connection Status Widget -->
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
    <main class="workspace" style="justify-content: center;">
        <div class="workspace-content-container" style="display: flex; justify-content: center; align-items: center; width: 100%;">
            <div class="step-content">
                <div class="workspace-header">
                    <h2 class="workspace-title">Please Sign In</h2>
                    <p class="workspace-subtitle">Identify yourself to initialize the local Audit</p>
                </div>

                <form method="POST" action="index.php?route=login/submit" class="form-group-stack">
                    <?php if (!empty($login_error)): ?>
                        <div style="background-color: #fef2f2; border: 1px solid #fee2e2; color: var(--color-danger); border-radius: 12px; padding: 0.75rem 1rem; font-size: 13px; font-weight: 700;">
                            <?php echo htmlspecialchars($login_error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-field">
                        <label class="form-label">Phone Number</label>
                        <div class="input-icon-wrapper">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <input type="tel" name="phone" id="loginPhone" class="form-input has-icon" placeholder="+233 24 000 0000" required>
                        </div>
                    </div>

                    <div class="form-field">
                        <label class="form-label">Email Address</label>
                        <div class="input-icon-wrapper">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            </span>
                            <input type="email" name="email" id="loginEmail" class="form-input has-icon" placeholder="auditor@melcomdc.com" required>
                        </div>
                    </div>

                    <div class="workspace-footer" style="border-top: none; padding-top: 0; margin-top: 0.5rem; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                            <span>Continue</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
