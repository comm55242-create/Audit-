<?php
// ==========================================================================
// MELCOM AUDIT SYSTEM - OTP VIEW
// Dedicated OTP Verification view showing premium alert banners & countdown.
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

$otp_code = isset($_SESSION['otp_code']) ? $_SESSION['otp_code'] : 'XXXX';
$otp_mode = isset($_SESSION['otp_mode']) ? $_SESSION['otp_mode'] : 'mock';
$timeLeft = isset($_SESSION['otp_expiry']) ? ($_SESSION['otp_expiry'] - time()) : 120;
if ($timeLeft < 0) $timeLeft = 0;
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
                    <span class="progress-number">1/8 Completed</span>
                </div>
                <div class="sidebar-progress-track">
                    <div class="sidebar-progress-fill" style="width: 12.5%;"></div>
                </div>
            </div>

            <!-- Checklist step nodes -->
            <nav class="sidebar-nav">
                <div class="step-node">
                    <div class="step-circle completed">✓</div>
                    <span class="step-text completed">Sign In</span>
                </div>
                <div class="step-node">
                    <div class="step-circle active">2</div>
                    <span class="step-text active">OTP Verification</span>
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
        <!-- Exit button -->
        <button type="button" onclick="cancelOtpFlow()" class="btn-exit" title="Exit Setup">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="workspace-content-container" style="display: flex; justify-content: center; align-items: center; width: 100%;">
            <div class="step-content">
                <div class="workspace-header">
                    <h2 class="workspace-title">OTP Verification</h2>
                    <p class="workspace-subtitle">Confirm security credentials before audit initialization</p>
                </div>

                <div class="otp-container">
                    <div id="otpInputRow" class="otp-row" style="display: flex; gap: 0.75rem; align-items: center;">
                        <div class="otp-input-group" style="display: flex; gap: 0.5rem; flex: 1;">
                            <input type="text" id="otpInput" class="otp-input" placeholder="OTP" maxlength="4" oninput="toggleConfirmBtnState()" <?php echo $timeLeft > 0 ? '' : 'disabled'; ?> style="flex: 1;">
                            <button type="button" id="btnConfirmOtp" class="btn-confirm-disabled" onclick="confirmOtpCode()" disabled style="flex-shrink: 0; min-width: 100px;">
                                Confirm
                            </button>
                        </div>
                        <button type="button" id="btnResendOtp" class="btn-otp-action" onclick="resendOtpCode()" style="display: <?php echo $timeLeft > 0 ? 'none' : 'block'; ?>; flex-shrink: 0; min-width: 110px;">
                            Resend OTP
                        </button>
                    </div>

                    <!-- Simulated OTP alert popup box -->
                    <div id="simulatedOtpAlert" class="otp-alert <?php echo ($otp_mode === 'mock' && $timeLeft > 0) ? '' : 'hidden'; ?>" style="margin-top: 1rem;">
                        <div class="otp-alert-info">
                            <span class="label">Simulated OTP:</span>
                            <span id="otpCodePlaceholder" class="code"><?php echo htmlspecialchars($otp_code); ?></span>
                        </div>
                        <span class="otp-alert-badge">Mock Mode</span>
                    </div>

                    <!-- OTP countdown timer display -->
                    <div id="otpTimerContainer" class="otp-timer-wrapper <?php echo $timeLeft > 0 ? '' : 'hidden'; ?>" style="margin-top: 1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 13px; font-weight: 700; color: var(--color-text-muted);">
                        <svg style="width:16px;height:16px;color:var(--color-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Code expires in: <span id="otpCountdown" style="color: var(--color-danger); min-width: 35px; display: inline-block;">02:00</span></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    let secondsLeft = <?php echo $timeLeft; ?>;
    let timerInterval = null;

    function toggleConfirmBtnState() {
        const val = document.getElementById("otpInput").value.trim();
        const btn = document.getElementById("btnConfirmOtp");
        if (val.length === 4 && secondsLeft > 0) {
            btn.disabled = false;
            btn.className = "btn btn-primary";
        } else {
            btn.disabled = true;
            btn.className = "btn-confirm-disabled";
        }
    }

    function startTimer() {
        if (timerInterval) clearInterval(timerInterval);
        const container = document.getElementById("otpTimerContainer");
        const countdown = document.getElementById("otpCountdown");
        
        if (secondsLeft <= 0) {
            handleExpiry();
            return;
        }

        container.classList.remove("hidden");
        updateDisplay();

        timerInterval = setInterval(() => {
            secondsLeft--;
            if (secondsLeft <= 0) {
                clearInterval(timerInterval);
                handleExpiry();
            } else {
                updateDisplay();
            }
        }, 1000);

        function updateDisplay() {
            const mins = Math.floor(secondsLeft / 60);
            const secs = secondsLeft % 60;
            countdown.innerText = `${mins < 10 ? "0" + mins : mins}:${secs < 10 ? "0" + secs : secs}`;
        }
    }

    function handleExpiry() {
        document.getElementById("otpInput").disabled = true;
        document.getElementById("otpInput").value = "";
        
        const btnConfirm = document.getElementById("btnConfirmOtp");
        btnConfirm.disabled = true;
        btnConfirm.className = "btn-confirm-disabled";
        
        document.getElementById("otpTimerContainer").classList.add("hidden");
        document.getElementById("simulatedOtpAlert").classList.add("hidden");
        document.getElementById("btnResendOtp").style.display = "block";
        
        alert("The OTP has expired! Please click Resend OTP to request a new code.");
    }

    function confirmOtpCode() {
        const val = document.getElementById("otpInput").value.trim();
        if (val.length !== 4) return;

        fetch(`index.php?route=otp/verify&code=${encodeURIComponent(val)}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                if (timerInterval) clearInterval(timerInterval);
                alert("OTP Confirmed successfully!");
                location.href = "index.php?route=dashboard";
            } else {
                alert("Verification Failed: " + data.message);
                document.getElementById("otpInput").value = "";
                toggleConfirmBtnState();
            }
        })
        .catch(err => {
            alert("Connection error: Could not verify OTP.");
        });
    }

    function resendOtpCode() {
        document.getElementById("btnResendOtp").style.display = "none";
        
        fetch(`index.php?route=otp/resend`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                secondsLeft = data.expires_in || 120;
                
                // Show alert banner for simulated mode
                const alertEl = document.getElementById("simulatedOtpAlert");
                if (data.mode === 'mock') {
                    document.getElementById("otpCodePlaceholder").innerText = data.code;
                    alertEl.classList.remove("hidden");
                } else {
                    alertEl.classList.add("hidden");
                }

                // Enable inputs
                document.getElementById("otpInput").disabled = false;
                document.getElementById("otpInput").value = "";
                document.getElementById("otpInput").focus();
                
                toggleConfirmBtnState();
                startTimer();
                alert("A new OTP code has been dispatched successfully.");
            } else {
                alert("Resend Failed: " + data.message);
                document.getElementById("btnResendOtp").style.display = "block";
            }
        })
        .catch(err => {
            alert("Connection error: Could not resend OTP.");
            document.getElementById("btnResendOtp").style.display = "block";
        });
    }

    function cancelOtpFlow() {
        if (confirm("Are you sure you want to cancel the validation process and return?")) {
            location.href = "index.php?route=logout";
        }
    }

    // Launch countdown timer on load
    startTimer();
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
